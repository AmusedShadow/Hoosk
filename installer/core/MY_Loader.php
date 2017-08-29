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
