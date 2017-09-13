<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// EXTENDS/MODIFIES LOADER CLASS TO BRANCH TO /CINCH/THEME DIRECTORY FOR PUBLIC FILES
// SEE AROUND LINE 65

class MY_Loader extends CI_Loader {
    public function __construct() {
        parent::__construct();
    }

    /**
     * CUSTOMISED Loader function
     *
     * This function is used to load views and files.
     * Variables are prefixed with _ci_ to avoid symbol collision with
     * variables made available to view files
     *
     * @param    array
     * @return    void
     */
    protected function _ci_load($_ci_data) {

        // Set the default data variables
        foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val) {
            $$_ci_val = (!isset($_ci_data[$_ci_val])) ? false : $_ci_data[$_ci_val];
        }

        $file_exists = false;

        // Set the path to the requested file
        if ($_ci_path != '') {
            $_ci_x    = explode('/', $_ci_path);
            $_ci_file = end($_ci_x);
        } else {
            $_ci_ext  = pathinfo($_ci_view, PATHINFO_EXTENSION);
            $_ci_file = ($_ci_ext == '') ? $_ci_view . '.php' : $_ci_view;

            foreach ($this->_ci_view_paths as $view_file => $cascade) {
                if (file_exists($view_file . $_ci_file)) {
                    $_ci_path    = $view_file . $_ci_file;
                    $file_exists = true;
                    break;
                }

                if (!$cascade) {
                    break;
                }
            }
        }

        if ((!defined('HOOSK_ADMIN')) && (defined('THEME'))) {
            $_ci_path = 'theme/' . THEME . '/' . $_ci_file;
        }
        ##########################################################################################

        if (!$file_exists && !file_exists($_ci_path)) {
            show_error('Unable to load the requested file: ' . $_ci_file);
        }

        // This allows anything loaded using $this->load (views, files, etc.)
        // to become accessible from within the Controller and Model functions.

        $_ci_CI = &get_instance();
        foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var) {
            if (!isset($this->$_ci_key)) {
                $this->$_ci_key = &$_ci_CI->$_ci_key;
            }
        }

        /*
         * Extract and cache variables
         *
         * You can either set variables using the dedicated $this->load_vars()
         * function or via the second parameter of this function. We'll merge
         * the two types and cache them so that views that are embedded within
         * other views can have access to these variables.
         */
        if (is_array($_ci_vars)) {
            $this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
        }
        extract($this->_ci_cached_vars);

        /*
         * Buffer the output
         *
         * We buffer the output for two reasons:
         * 1. Speed. You get a significant speed boost.
         * 2. So that the final rendered template can be
         * post-processed by the output class.  Why do we
         * need post processing?  For one thing, in order to
         * show the elapsed page load time.  Unless we
         * can intercept the content right before it's sent to
         * the browser and then stop the timer it won't be accurate.
         */
        ob_start();

        // If the PHP installation does not support short tags we'll
        // do a little string replacement, changing the short tags
        // to standard PHP echo statements.

        if ((bool) @ini_get('short_open_tag') === false and config_item('rewrite_short_tags') == true) {
            echo eval('?>' . preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
        } else {
            include $_ci_path; // include() vs include_once() allows for multiple views with the same name
        }

        log_message('debug', 'File loaded: ' . $_ci_path);

        // Return the file data if requested
        if ($_ci_return === true) {
            $buffer = ob_get_contents();
            @ob_end_clean();
            return $buffer;
        }

        /*
         * Flush the buffer... or buff the flusher?
         *
         * In order to permit views to be nested within
         * other views, we need to flush the content back out whenever
         * we are beyond the first level of output buffering so that
         * it can be seen and included properly by the first included
         * template and any subsequent ones. Oy!
         *
         */
        if (ob_get_level() > $this->_ci_ob_level + 1) {
            ob_end_flush();
        } else {
            $_ci_CI->output->append_output(ob_get_contents());
            @ob_end_clean();
        }
    }

    /**
     * Database Loader
     *
     * @param   mixed   $params     Database configuration options
     * @param   bool    $return     Whether to return the database object
     * @param   bool    $query_builder  Whether to enable Query Builder
     *                  (overrides the configuration setting)
     *
     * @return  object|bool Database object if $return is set to TRUE,
     *                  FALSE on failure, CI_Loader instance in any other case
     */
    public function database($params = '', $return = FALSE, $query_builder = NULL) {
        // Grab the super object
        $CI = &get_instance();
        if (!isset($CI->capsule)) {
            $CI->load->library('capsule');
        }

        return parent::database($params, $return, $query_builder);
    }

    /**
     * Model Loader
     *
     * Loads and instantiates models.
     *
     * @param   string  $model      Model name
     * @param   string  $name       An optional object name to assign to
     * @param   bool    $db_conn    An optional database connection configuration to initialize
     * @return  object
     */
    public function EloquentModel($model, $name = '', $db_conn = FALSE) {
        if (empty($model)) {
            return $this;
        } elseif (is_array($model)) {
            foreach ($model as $key => $value) {
                is_int($key) ? $this->model($value, '', $db_conn) : $this->model($key, $value, $db_conn);
            }

            return $this;
        }

        $path = '';

        // Is the model in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($model, '/')) !== FALSE) {
            // The path is in front of the last slash
            $path = substr($model, 0, ++$last_slash);

            // And the model name behind it
            $model = substr($model, $last_slash);
        }

        if (empty($name)) {
            $name = $model;
        }

        if (in_array($name, $this->_ci_models, TRUE)) {
            return $this;
        }

        $CI = &get_instance();
        if (isset($CI->$name)) {
            throw new RuntimeException('The model name you are loading is the name of a resource that is already being used: ' . $name);
        }

        if ($db_conn !== FALSE && !class_exists('CI_DB', FALSE)) {
            if ($db_conn === TRUE) {
                $db_conn = '';
            }

            $this->database($db_conn, FALSE, TRUE);
        }

        // Note: All of the code under this condition used to be just:
        //
        //       load_class('Model', 'core');
        //
        //       However, load_class() instantiates classes
        //       to cache them for later use and that prevents
        //       MY_Model from being an abstract class and is
        //       sub-optimal otherwise anyway.
        if (!class_exists('CI_Model', FALSE)) {
            $app_path = APPPATH . 'core' . DIRECTORY_SEPARATOR;
            if (file_exists($app_path . 'Model.php')) {
                require_once $app_path . 'Model.php';
                if (!class_exists('CI_Model', FALSE)) {
                    throw new RuntimeException($app_path . "Model.php exists, but doesn't declare class CI_Model");
                }
            } elseif (!class_exists('CI_Model', FALSE)) {
                require_once BASEPATH . 'core' . DIRECTORY_SEPARATOR . 'Model.php';
            }

            $class = config_item('subclass_prefix') . 'Model';
            if (file_exists($app_path . $class . '.php')) {
                require_once $app_path . $class . '.php';
                if (!class_exists($class, FALSE)) {
                    throw new RuntimeException($app_path . $class . ".php exists, but doesn't declare class " . $class);
                }
            }
        }

        $model = ucfirst($model);
        if (!class_exists($model, FALSE)) {
            foreach ($this->_ci_model_paths as $mod_path) {
                if (!file_exists($mod_path . 'models/' . $path . $model . '.php')) {
                    continue;
                }

                require_once $mod_path . 'models/' . $path . $model . '.php';
                if (!class_exists($model, FALSE)) {
                    throw new RuntimeException($mod_path . "models/" . $path . $model . ".php exists, but doesn't declare class " . $model);
                }

                break;
            }

            if (!class_exists($model, FALSE)) {
                throw new RuntimeException('Unable to locate the model you have specified: ' . $model);
            }
        }

        if (!is_subclass_of($model, 'Eloquent')) {
            throw new RuntimeException("Class " . $model . " doesn't extend Eloquent");
        }

        $this->_ci_models[] = $name;
        $obName             = trim(strtolower($name));
        $CI->$obName        = new $model();
        return $this;
    }
}
