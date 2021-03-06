<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Installer extends CI_Controller {
    protected $salt = '';

    /**
     * construct
     * Class construct
     *
     * @access public
     */
    public function __construct() {
        //call the parent construct
        parent::__construct();

        //if the config file exists already error out
        if (file_exists(FCPATH . 'config.php')) {
            show_error('The installer has already run!');
        }

        //without a timezone set php flakes
        $dtz = @date_default_timezone_get();
        //setup the default timezone
        date_default_timezone_set($dtz);

        //load the url helper (I don't remember why?)
        $this->load->helper('url');
    }

    /**
     * index
     * The index method
     *
     * @access public
     */
    public function index() {
        //load the form validation library
        $this->load->library('form_validation');

        //setup our form validation rules
        $this->form_validation->set_rules('siteName', 'Site Name', 'required');
        $this->form_validation->set_rules('siteURL', 'Site URL', 'required');
        $this->form_validation->set_rules('dbHost', 'Database Hostname', 'required');
        $this->form_validation->set_rules('dbUserName', 'Database Username', 'required');
        $this->form_validation->set_rules('dbPass', 'Database Password', 'required');
        $this->form_validation->set_rules('dbName', 'Database Name', 'required|callback__tryDatabaseConnect|callback__tryConfigWrite');
        $this->form_validation->set_rules('timezone', 'Timezone', 'required');
        $this->form_validation->set_rules('defaultUsername', 'Default Username', 'required');
        $this->form_validation->set_rules('defaultPassword', 'Default Password', 'required');

        //if the validation hasn't run or returned falsed lets load the installer view
        if ($this->form_validation->run() === false) {
            //list of time zones and such
            $this->timezones();

            //load the view
            $this->load->view('installer/install');
        } else {
            //if the form validation has run successfully then lets do this!
            $this->_buildConfigFile(); //build the configuration file and save it
            $this->_runMigrations(); //setup sql tables and such
            $this->_fixDefaultAccount(); //removes the existing demo account and creates a new one
            $this->load->view('installer/congrats'); //load our congrats view
        }
    }

    /**
     * _tryConfigWrite
     * Make sure we are able to write a configuration file
     *
     * @access public
     * @return bool
     */
    public function _tryConfigWrite() {
        $path = FCPATH . 'config.php';

        file_put_contents($path, '--');
        $value = file_exists($path);
        if ($value == false) {
            $this->form_validation->set_message('_tryConfigWrite', 'Unable to write configuration to: ' . FCPATH . 'config.php');
            return false;
        }

        return true;
    }

    /**
     * _tryDatabaseConnect
     * This is used as a form validation method. Since CodeIgniter
     * allows us to dynamically define a db connection we do it
     * here, turning off errors, and the check to see if the
     * db class threw an exception. If it didn't we are all
     * good.
     *
     * @access public
     */
    public function _tryDatabaseConnect() {
        //get our post data
        $postData = $this->_getPostVars();

        //setup our config data
        $testConfig = array(
            'hostname'    => $postData['dbHost'],
            'username'    => $postData['dbUserName'],
            'password'    => $postData['dbPass'],
            'database'    => $postData['dbName'],
            'dbdriver'    => 'pdo',
            'subdriver'   => 'mysql',
            'dbprefix'    => '',
            'pconnection' => false,
            'db_debug'    => false,
            'cache_on'    => false,
            'cachedir'    => '',
            'char_set'    => 'utf8',
            'dbcollat'    => 'utf8_general_ci',
            'autoinit'    => false,
        );

        $cfg['default'] = $testConfig;
        $this->config->set_item('database', $cfg);

        //if we have all of the required information lets try testing our connection
        if ((!empty($testConfig['hostname'])) && (!empty($testConfig['username']))
            && (!empty($testConfig['password'])) && (!empty($testConfig['database']))) {
            //load the database
            $this->load->database($testConfig);
            try {
                //try init it
                $this->db->initialize();
            } catch (\Exception $e) {
                //if we have an exception throw an form validation error
                $this->form_validation->set_message('_tryDatabaseConnect', 'The SQL credentials you provided didn\'t seem to work :(');
                return false;
            }

            //everything worked return true
            return true;
        } else {
            //we should return false but that will throw an error on form validation
            //we don't care about returning an error here because the rules for the
            //fields above will throw errors for us since all those fields are required
            return true;
        }
    }

    /**
     * _buildConfigFile
     * Builds and saves the configuration file
     *
     * @access public
     */
    protected function _buildConfigFile() {
        //load the string helper
        $this->load->helper('string');

        //build our salt string
        $this->salt = random_string('alnum', 180);

        //our config lines
        $lines = array(
            '<?php',
            '',
            '//database details',
            "define('DB_HOST','{db_hostname}'); //database hostname",
            "define('DB_USERNAME','{db_username}'); //database username",
            "define('DB_PASS','{db_password}'); //database password",
            "define('DB_NAME','{db_name}'); //database name",
            "define('DB_DRIVER','{db_driver}');",
            '',
            "//url details",
            "define('BASE_URL','http://{url_base}'); //base url",
            "define('EMAIL_URL','http://{url_email}'); //email/cookie url",
            '',
            '//base settings',
            "define('ADMIN_THEME',BASE_URL . '/theme/admin');",
            "define('SALT','" . $this->salt . "');",
            "define('RSS_FEED',true);",
            "define('SITENAME_TXT','{site_name}');",
            '',
            '$assign_to_config[\'encryption_key\'] = \'' . random_string('alnum', 32) . '\'; //custom encryption key',
            '',
            'date_default_timezone_set(\'' . $this->input->post('timezone') . '\');',
            '',
        );

        //implode our lines by a new line character
        $fileData = implode(PHP_EOL, $lines);

        //get the post data
        $postData = $this->_getPostVars();

        //replace our temp variables
        $fileData = str_replace('{db_hostname}', str_replace("'", "\'", $postData['dbHost']), $fileData);
        $fileData = str_replace('{db_username}', str_replace("'", "\'", $postData['dbUserName']), $fileData);
        $fileData = str_replace('{db_password}', str_replace("'", "\'", $postData['dbPass']), $fileData);
        $fileData = str_replace('{db_name}', str_replace("'", "\'", $postData['dbName']), $fileData);
        $fileData = str_replace('{site_name}', str_replace("'", "\'", $postData['siteName']), $fileData);
        $fileData = str_replace('{url_base}', str_replace("'", "\'", $postData['siteUrl']), $fileData);
        $fileData = str_replace('{url_email}', str_replace("'", "\'", $postData['siteUrl']), $fileData);
        $fileData = str_replace('{db_driver}', 'mysql', $fileData);

        //save our file
        file_put_contents(FCPATH . 'config.php', $fileData);
    }

    /**
     * _getPostVars
     * Returns the post vars
     *
     * @access public
     * @return array
     */
    protected function _getPostVars() {
        $postData = array(
            'dbHost'     => $this->input->post('dbHost'),
            'dbUserName' => $this->input->post('dbUserName'),
            'dbPass'     => $this->input->post('dbPass'),
            'dbName'     => $this->input->post('dbName'),
            'siteName'   => $this->input->post('siteName'),
            'siteUrl'    => $this->input->post('siteURL'),
        );

        //does the siteURL contain https://?
        if (substr($postData['siteUrl'], 0, 8) == 'https://') {
            $postData['siteUrl'] = substr($postData['siteUrl'], 8);
        }

        //does the siteURL contain http://?
        if (substr($postData['siteUrl'], 0, 7) == 'http://') {
            $postData['siteUrl'] = substr($postData['siteUrl'], 7);
        }

        //lets trim / off the left side
        $postData['siteUrl'] = ltrim($postData['siteUrl'], '/');

        //lets trim / off the right side
        $postData['siteUrl'] = rtrim($postData['siteUrl'], '/');

        //strip off the index page if it exists
        $item = '/' . $this->config->item('index_page');
        if (!empty($item)) {
            $length = 0 - strlen($item);
            if (substr($postData['siteUrl'], $length) == $item) {
                $postData['siteUrl'] = substr($postData['siteUrl'], 0, $length);
            }
        }

        return $postData;
    }

    /**
     * _remap
     * CodeIgniter remap command
     *
     * @acecss public
     */
    public function _remap($method, $params = array()) {
        $this->index();
    }

    protected function _runMigrations() {
        $this->load->library('Schema');
        $this->load->library('migration');

        if ($this->migration->current() === FALSE) {
            show_error($this->migration->error_string());
        }
    }

    protected function _fixDefaultAccount() {
        define('SALT', $this->salt);

        $this->load->model('hoosk_model');

        $this->hoosk_model->createUser($this->input->post('defaultUsername'), 'me@example.com', $this->input->post('defaultPassword'));
    }

    protected function timezones() {
        $tz       = DateTimeZone::listIdentifiers();
        $compiled = array();
        foreach ($tz as $row) {
            $compiled[$row] = $row;
        }

        $this->load->vars('timezones', $compiled);
    }
}
