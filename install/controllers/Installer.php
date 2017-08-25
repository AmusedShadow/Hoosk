<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license    http://opensource.org/licenses/MIT    MIT License
 * @link    http://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Libraries
 * @author        EllisLab Dev Team
 * @link        http://codeigniter.com/user_guide/general/controllers.html
 */
class Installer extends CI_Controller {
    protected $DB   = '';
    protected $salt = '';

    public function __construct() {
        parent::__construct();
        date_default_timezone_set(@date_default_timezone_get());

        $configPaths[] = APPPATH . 'config/hoosk.php';
        if (defined('ENVIRONMENT')) {
            $configPaths[] = APPPATH . 'config/' . ENVIRONMENT . '/hoosk.php';
        }

        $configFound = false;
        foreach ($configPaths as $cPath) {
            if (file_exists($cPath)) {
                $configFound = true;
                break;
            }
        }

        if ($configFound == true) {
            show_error('The installation process has already run.');
        }
    }

    public function index() {
        $this->load->helper('url');

        //baa we have to "define" hooskadmin or loading views wont work right
        define('HOOSK_ADMIN', 1);

        //we only want to install if we havne't already
        if (file_exists(FCPATH . 'config.php')) {
            //redirect the user to the homepage
            redirect('/index.php');
            exit;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('siteName', 'Site Name', 'required');
        $this->form_validation->set_rules('siteURL', 'Site URL', 'required'); // (without http:// or trailing slash)
        $this->form_validation->set_rules('dbName', 'Database Name', 'required');
        $this->form_validation->set_rules('dbUserName', 'Database Username', 'required');
        $this->form_validation->set_rules('dbPass', 'Database Password', 'required'); //technically this isn't required
        $this->form_validation->set_rules('dbDriver', 'Database Driver', 'required|callback__validDriver');
        $this->form_validation->set_rules('dbHost', 'Database Host', 'required|callback__testDbConnection');

        if ($this->form_validation->run() === false) {
            $this->load->view('installer/install');
        } else {
            $this->load->helper('string'); //this will be used to generate our salt
            $this->salt = random_string('alnum', 150); //generate our salt
            $this->schema();
            $this->buildConfig();

            /*
            $catchAll = array();
            $queries  = $this->DB->queries;
            $times    = $this->DB->query_times;
            for ($a = 0; $a < $this->DB->query_count; $a++) {
            if ($times[$a] == 0) {
            $catchAll[] = $queries[$a];
            }
            }

            if (count($catchAll) > 0) {
            $catchAll = implode('<br /><br />', $catchAll);
            show_error($catchAll);
            } else {
            $this->load->view('installer/congrats');
            }
             */

            $this->load->view('installer/congrats');
        }
    }

    protected function buildConfig() {
        $url = $this->input->post('siteURL');
        $url = rtrim($url, '/');

        if (substr($url, 0, 8) == 'https://') {
            $url = substr($url, 8);
        } elseif (substr($url, 0, 7) == 'http://') {
            $url = substr($url, 7);
        }

        $url = ltrim($url, '/');

        $config = array(
            'DB_HOST'       => $this->input->post('dbHost'),
            'DB_USERNAME'   => $this->input->post('dbUserName'),
            'DB_PASS'       => $this->input->post('dbPass'),
            'DB_NAME'       => $this->input->post('dbName'),
            'DB_DRIVER'     => $this->input->post('dbDriver'),
            'BASE_URL'      => $url,
            'EMAIL_URL'     => $url,
            'SITE_NAME_TXT' => $this->input->post('siteName'),
            'SALT'          => $this->salt,
            'ADMIN_THEME'   => $url . '/theme/admin',
            'RSS_FEED'      => 'true',
        );

        $configFile = '<?php' . PHP_EOL . PHP_EOL;
        foreach ($config as $name => $value) {
            $configFile .= '$config[\'' . $name . '\'] = ' . '\'' . str_replace("'", "\\'", $value) . '\';' . PHP_EOL;
        }

        $configFile .= PHP_EOL . PHP_EOL;
        $configFile .= 'foreach ($config as $name => $value) {' . PHP_EOL;
        $configFile .= '    define($name,$value);' . PHP_EOL;
        $configFile .= '}' . PHP_EOL;

        //file_put_contents(APPPATH . 'config' . DIRECTORY_SEPARATOR . 'hoosk.php', $configFile);
        file_put_Contents(FCPATH . 'config.php', $configFile);
    }

    public function _validDriver($str = '') {
        $str  = trim(strtolower($str));
        $path = FCPATH . 'system/database/drivers/pdo/subdrivers/pdo_' . $str . '_driver.php';
        if (!file_exists($path)) {
            $this->form_validation->set_message('_validDriver', 'The SQL driver you selected is not valid!');
            return false;
        }

        return true;
    }

    public function _testDbConnection() {
        if ($this->_validDriver($this->input->post('dbDriver')) == false) {
            return true;
        }

        $testConfig = array(
            'hostname'    => $this->input->post('dbHost'),
            'username'    => $this->input->post('dbUserName'),
            'password'    => $this->input->post('dbPass'),
            'database'    => $this->input->post('dbName'),
            'dbdriver'    => 'pdo',
            'subdriver'   => $this->input->post('dbDriver'),
            'dbprefix'    => '',
            'pconnection' => false,
            'db_debug'    => false,
            'cache_on'    => false,
            'cachedir'    => '',
            'char_set'    => 'utf8',
            'dbcollat'    => 'utf8_general_ci',
            'autoinit'    => false,
        );

        if ((!empty($testConfig['hostname'])) && (!empty($testConfig['username']))
            && (!empty($testConfig['password'])) && (!empty($testConfig['database']))) {
            $this->DB = $this->load->database($testConfig, true);

            $connected = $this->DB->initialize();
            if (!$connected) {
                $this->form_validation->set_message('_testDbConnection', 'The SQL credentials you provided didn\'t seem to work :(');
                return false;
            }
        } else {
            //we should return false but that will throw an error on form validation
            //we don't care about returning an error here because the rules for the
            //fields above will throw errors for us since all those fields are required
            return true;
        }
    }

    protected function schema() {
        $this->load->library('schema');

        //create tables
        $this->hoosk_banner();
        $this->hoosk_navigation();
        $this->hoosk_page_attributes();
        $this->hoosk_page_content();
        $this->hoosk_page_meta();
        $this->hoosk_post();
        $this->hoosk_post_category();
        $this->hoosk_sessions();
        $this->hoosk_settings();
        $this->hoosk_social();
        $this->hoosk_user();

        //add our default data
    }

    public function _remap($method, $params = array()) {
        //always call index
        return $this->index();
    }

    protected function hoosk_banner() {
        Schema::create_table('hoosk_banner', function ($table) {
            $table->auto_increment_integer('slideID');
            $table->bigint('pageID');
            $table->string('slideImage', 350);
            $table->string('slideLink', 350);
            $table->string('slideAlt', 350);
            $table->integer('slideOrder');
        }, $this->DB);
    }

    protected function hoosk_navigation() {
        Schema::create_table('hoosk_navigation', function ($table) {
            $table->auto_increment_integer('navID');
            $table->string('navSlug', 10);
            $table->text('navTitle');
            $table->text('navHTML');
            $table->text('navEdit');
        }, $this->DB);

        $data = array(
            array(
                'navSlug'  => 'header',
                'navTitle' => 'Header Nav',
                'navHTML'  => '<ul class="nav navbar-nav"><li><a href="http://beta.hoosk.org">Home</a></li><li><a href="/contact">Contact</a></li><li><a href="/news">News</a></li></ul>',
                'navEdit'  => '<li class="dd-item" data-href="home" data-title="Home"><a class="right" onclick="var li = this.parentNode; var ul = li.parentNode; ul.removeChild(li);"><i class="fa fa-remove"></i></a><div class="dd-handle">Home</div></li><li class="dd-item" data-href="contact" data-title="Contact"><a class="right" onclick="var li = this.parentNode; var ul = li.parentNode; ul.removeChild(li);"><i class="fa fa-remove"></i></a><div class="dd-handle">Contact</div></li><li class="dd-item" data-href="news" data-title="News"><a class="right" onclick="var li = this.parentNode; var ul = li.parentNode; ul.removeChild(li);"><i class="fa fa-remove"></i></a><div class="dd-handle">News</div></li>',
            ),
            array(
                'navSlug'  => 'test',
                'navTitle' => 'test',
                'navHTML'  => '<ul class="nav navbar-nav"></ul>',
                'navEdit'  => '',
            ),
        );

        foreach ($data as $seed) {
            $this->DB->insert('hoosk_navigation', $seed);
        }
    }

    protected function hoosk_page_attributes() {
        Schema::create_table('hoosk_page_attributes', function ($table) {
            $table->auto_increment_integer('attributeID');
            $table->bigint('pageID');
            $table->integer('pagePublished');
            $table->bigint('pageParent');
            $table->string('pageTemplate', 250);
            $table->integer('pageBanner');
            $table->string('pageURL', 250);
            $table->tinyint('enableJumbotron');
            $table->tinyint('enableSlider');
            $table->tinyint('enableSearch');
            $table->datetime('pageUpdated');
        }, $this->DB);

        $data = array(
            array(
                'pageID'          => '1',
                'pagePublished'   => '1',
                'pageParent'      => '0',
                'pageTemplate'    => 'home',
                'pageBanner'      => '0',
                'pageURL'         => 'home',
                'enableJumbotron' => '1',
                'enableSlider'    => '0',
                'enableSearch'    => '1',
                'pageUpdated'     => '2017-03-06 21:06:09',
            ),
            array(
                'pageID'          => '2',
                'pagePublished'   => '1',
                'pageParent'      => '0',
                'pageTemplate'    => 'page',
                'pageBanner'      => '0',
                'pageURL'         => 'contact',
                'enableJumbotron' => '1',
                'enableSlider'    => '0',
                'enableSearch'    => 0,
                'pageUpdated'     => '2015-01-09 07:09:42',
            ),
            array(
                'pageID'          => '3',
                'pagePublished'   => '1',
                'pageParent'      => '0',
                'pageTemplate'    => 'news',
                'pageBanner'      => '0',
                'pageURL'         => 'news',
                'enableJumbotron' => '0',
                'enableSlider'    => '0',
                'enableSearch'    => '1',
                'pageUpdated'     => '2016-10-10 20:44:01',
            ),
        );

        foreach ($data as $seed) {
            $this->DB->insert('hoosk_page_attributes', $seed);
        }
    }

    protected function hoosk_page_content() {
        Schema::create_table('hoosk_page_content', function ($table) {
            $table->auto_increment_integer('pageID');
            $table->text('pageTitle');
            $table->text('navTitle');
            $table->text('pageContent');
            $table->text('pageContentHTML');
            $table->text('jumbotron');
            $table->text('jumbotronHTML');
            $table->datetime('pageCreated');
        }, $this->DB);

        $data = array(
            array(
                'pageTitle'       => 'Hoosk Demo',
                'navTitle'        => 'Home',
                'pageContent'     => '{"data":[{"type":"columns","data":{"columns":[{"width":6,"blocks":[{"type":"heading","data":{"text":"This is the Hoosk demo site.\\n","heading":""}},{"type":"text","data":{"text":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.\\n\\n"}},{"type":"text","data":{"text":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortkjor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.\\n\\n"}}]},{"width":6,"blocks":[{"type":"image_extended","data":{"file":{"url":"http://beta.hoosk.org/images/responsive_hoosk.png","filename":"responsive_hoosk.png"},"caption":"Hoosk is responsive","source":""}}]}],"preset":"columns-6-6"}}]}',
                'pageContentHTML' => '<div class=\'row\'><div class=\'col-md-6\'><>This is the Hoosk demo site.
</><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.</p>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortkjor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.</p>
</div><div class=\'col-md-6\'><img class="img-responsive" src="http://beta.hoosk.org/images/responsive_hoosk.png" alt="Hoosk is responsive" />
</div></div>',
                'jumbotron'       => '{"data":[{"type":"image_extended","data":{"file":{"url":"http://beta.hoosk.org/images/large_logo.png","filename":"large_logo.png"},"caption":"Hoosk Emblem","source":""}},{"type":"image_extended","data":{"file":{"url":"http://beta.hoosk.org/images/welcome_to_hoosk.png","filename":"welcome_to_hoosk.png"},"caption":"welcome to hoosk","source":""}},{"type":"text","data":{"text":"This demo resets every half hour, the login details are:\\n\\n"}},{"type":"columns","data":{"columns":[{"width":6,"blocks":[{"type":"text","data":{"text":"Username \\\\- demo\\n\\n"}}]},{"width":6,"blocks":[{"type":"text","data":{"text":"Password \\\\- demo\\n\\n"}}]}],"preset":"columns-6-6"}},{"type":"button","data":{"size":"btn-lg","style":"btn-primary","is_block":false,"url":"/admin","null":"0","html":"Login!"}}]}',
                'jumbotronHTML'   => '<img class="img-responsive" src="http://beta.hoosk.org/images/large_logo.png" alt="Hoosk Emblem" />
<img class="img-responsive" src="http://beta.hoosk.org/images/welcome_to_hoosk.png" alt="welcome to hoosk" />
<p>This demo resets every half hour, the login details are:</p>
<div class=\'row\'><div class=\'col-md-6\'><p>Username &#45; demo</p>
</div><div class=\'col-md-6\'><p>Password &#45; demo</p>
</div></div><a href="/admin" class="btn btn-primary btn-lg">Login!</a>',
                'pageCreated'     => '2014-11-03 02:22:20',
            ),
            array(
                'pageTitle'       => 'Contact',
                'navTitle'        => 'Contact',
                'pageContent'     => '{"data":[{"type":"heading","data":{"text":"Contact"}},{"type":"text","data":{"text":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.\\n"}}]}',
                'pageContentHTML' => '<h2>Contact</h2>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.</p>',
                'jumbotron'       => '{"data":[{"type":"image_extended","data":{"file":{"url":"http://beta.hoosk.org/images/large_logo.png","filename":"large_logo.png"},"caption":"Hoosk Emblem","source":""}}]}',
                'jumbotronHTML'   => '<img class="img-responsive" src="http://beta.hoosk.org/images/large_logo.png" alt="Hoosk Emblem" />',
                'pageCreated'     => '2014-11-04 11:54:54',
            ),
            array(
                'pageTitle'       => 'News',
                'navTitle'        => 'News',
                'pageContent'     => '',
                'pageContentHTML' => '',
                'jumbotron'       => '',
                'jumbotronHTML'   => '',
                'pageCreated'     => '2014-12-03 06:47:20',
            ),
        );

        foreach ($data as $seed) {
            $this->DB->insert('hoosk_page_content', $seed);
        }
    }

    protected function hoosk_page_meta() {
        Schema::create_table('hoosk_page_meta', function ($table) {
            $table->auto_increment_integer('metaID');
            $table->bigint('pageID');
            $table->text('pageKeywords');
            $table->text('pageDescription');
        }, $this->DB);

        $data = array(
            array(
                'pageID'          => '1',
                'pageKeywords'    => 'Hoosk Keywords',
                'pageDescription' => 'Hoosk Description',
            ),
            array(
                'pageID'          => '9',
                'pageKeywords'    => 'Contact',
                'pageDescription' => 'Contact',
            ),
            array(
                'pageID'          => '10',
                'pageKeywords'    => 'test',
                'pageDescription' => 'test',
            ),
        );

        foreach ($data as $seed) {
            $this->DB->insert('hoosk_page_meta', $seed);
        }
    }

    protected function hoosk_post() {
        Schema::create_table('hoosk_post', function ($table) {
            $table->auto_increment_integer('postID');
            $table->text('postURL');
            $table->text('postTitle');
            $table->text('postExcerpt');
            $table->text('postContentHTML');
            $table->text('postContent');
            $table->text('postImage');
            $table->bigint('categoryID');
            $table->integer('published');
            $table->string('datePosted', 100);
            $table->bigint('unixStamp');
        }, $this->DB);

        $data = array(
            array(
                'postURL'         => 'hello_hoosk',
                'postTitle'       => 'Hello Hoosk.',
                'postExcerpt'     => 'Brain freeze. Kinda hot in these rhinos. Here she comes to wreck the day. Brain freeze. Excuse me, I\'d like to ASS you a few questions.',
                'postContentHTML' => '<div class=\'row\'><div class=\'col-md-6\'><p>Brain freeze. Kinda hot in these rhinos. Here she comes to wreck the day. Brain freeze. Excuse me, I\'d like to ASS you a few questions. We\'re going for a ride on the information super highway. Your entrance was good, his was better. Kinda hot in these rhinos. It\'s because i\'m green isn\'t it! Here she comes to wreck the day. Alrighty Then Excuse me, I\'d like to ASS you a few questions. </p>
<a href="www.google.com" class="btn btn-default ">Button</a>
</div><div class=\'col-md-6\'><p>Your entrance was good, his was better. We got no food we got no money and our pets heads are falling off! Haaaaaaarry. Look at that, it\'s exactly three seconds before I honk your nose and pull your underwear over your head. It\'s because i\'m green isn\'t it! Hey, maybe I will give you a call sometime. Your number still 911? Excuse me, I\'d like to ASS you a few questions. </p>
</div></div>',
                'postContent'     => '{"data":[{"type":"columns","data":{"columns":[{"width":6,"blocks":[{"type":"text","data":{"text":"Brain freeze. Kinda hot in these rhinos. Here she comes to wreck the day. Brain freeze. Excuse me, I\'d like to ASS you a few questions. We\'re going for a ride on the information super highway. Your entrance was good, his was better. Kinda hot in these rhinos. It\'s because i\'m green isn\'t it! Here she comes to wreck the day. Alrighty Then Excuse me, I\'d like to ASS you a few questions. \\n"}},{"type":"button","data":{"size":"","style":"btn-default","is_block":false,"url":"www.google.com","null":"0","html":"Button"}}]},{"width":6,"blocks":[{"type":"text","data":{"text":"Your entrance was good, his was better. We got no food we got no money and our pets heads are falling off! Haaaaaaarry. Look at that, it\'s exactly three seconds before I honk your nose and pull your underwear over your head. It\'s because i\'m green isn\'t it! Hey, maybe I will give you a call sometime. Your number still 911? Excuse me, I\'d like to ASS you a few questions. \\n"}}]}],"preset":"columns-6-6"}}]}',
                'postImage'       => 'large_logo.png',
                'categoryID'      => '1',
                'published'       => '0',
                'datePosted'      => '12/17/2016 22:12:23',
                'unixStamp'       => '1482012743',
            ),
            array(
                'postURL'         => 'me_im_dishonest',
                'postTitle'       => 'Me? I\'m dishonest',
                'postExcerpt'     => 'A drug person can learn to cope with things like seeing their dead grandmother crawling up their leg with a knife in her teeth. But no one should be asked to handle this trip. Well, then, I confess, it is my intention to commandeer one of these ships, pick up a crew in Tortuga, raid, pillage, plunder and otherwise pilfer my weasely black guts out.',
                'postContentHTML' => '',
                'postContent'     => '',
                'postImage'       => 'responsive_hoosk.png',
                'categoryID'      => '3',
                'published'       => '1',
                'datePosted'      => '06/12/2014 02:58',
                'unixStamp'       => '1402538280',
            ),
            array(
                'postURL'         => 'yes_i_used_a_machine_gun',
                'postTitle'       => 'Yes, I used a machine gun.',
                'postExcerpt'     => 'You wouldn\'t hit a man with no trousers on, would you? You\'re only supposed to blow the bloody doors off! You know, your bobby dangler, giggle stick, your general-two-colonels, master of ceremonies... Yeah,',
                'postContentHTML' => '<div class=\'row\'><div class=\'col-md-6\'><p>You\'re only supposed to blow the bloody doors off! Jasper: Your baby is the miracle the whole world has been waiting for. Yes, I used a machine gun. You know, your bobby dangler, giggle stick, your general&#45;two&#45;colonels, master of ceremonies... Yeah, don\'t be shy, let\'s have a look. My lord! You\'re a tripod. My lord! You\'re a tripod. I took a Viagra, got stuck in me throat, I\'ve had a stiff neck for hours. When I get back, remind to tell you about the time I took 100 nuns to Nairobi! At this point, I\'d set you up with a chimpanzee if it\'d brought you back to the world! Pull my finger! It\'s not the size mate, it\'s how you use it. You wouldn\'t hit a man with no trousers on, would you? </p>
</div><div class=\'col-md-6\'><p>Your were only supposed to blow the bloody doors off. My lord! You\'re a tripod. When I get back, remind to tell you about the time I took 100 nuns to Nairobi! It\'s not the size mate, it\'s how you use it. At this point, I\'d set you up with a chimpanzee if it\'d brought you back to the world! </p>
<>Hola Mundo!!!</></div></div>',
                'postContent'     => '{"data":[{"type":"columns","data":{"columns":[{"width":6,"blocks":[{"type":"text","data":{"text":" You\'re only supposed to blow the bloody doors off! Jasper: Your baby is the miracle the whole world has been waiting for. Yes, I used a machine gun. You know, your bobby dangler, giggle stick, your general\\\\-two\\\\-colonels, master of ceremonies... Yeah, don\'t be shy, let\'s have a look. My lord! You\'re a tripod. My lord! You\'re a tripod. I took a Viagra, got stuck in me throat, I\'ve had a stiff neck for hours. When I get back, remind to tell you about the time I took 100 nuns to Nairobi! At this point, I\'d set you up with a chimpanzee if it\'d brought you back to the world! Pull my finger! It\'s not the size mate, it\'s how you use it. You wouldn\'t hit a man with no trousers on, would you? \\n\\n"}}]},{"width":6,"blocks":[{"type":"text","data":{"text":"Your were only supposed to blow the bloody doors off. My lord! You\'re a tripod. When I get back, remind to tell you about the time I took 100 nuns to Nairobi! It\'s not the size mate, it\'s how you use it. At this point, I\'d set you up with a chimpanzee if it\'d brought you back to the world! \\n"}},{"type":"heading","data":{"text":"Hola Mundo!!!","heading":""}}]}],"preset":"columns-6-6"}}]}',
                'postImage'       => 'jumbotron.jpg',
                'categoryID'      => '4',
                'published'       => '1',
                'datePosted'      => '06/12/2014 03:30',
                'unixStamp'       => '1402540200',
            ),
        );

        foreach ($data as $seed) {
            $this->DB->insert('hoosk_post', $seed);
        }
    }

    protected function hoosk_post_category() {
        Schema::create_table('hoosk_post_category', function ($table) {
            $table->auto_increment_integer('categoryID');
            $table->text('categoryTitle');
            $table->text('categorySlug');
            $table->text('categoryDescription');
        }, $this->DB);

        $data = array(
            array(
                'categoryTitle'       => 'Uncategorized',
                'categorySlug'        => 'uncategorized-asd',
                'categoryDescription' => 'This is the default category for things that dont quite fit anywhere',
            ),
            array(
                'categoryTitle'       => 'Hoosk Updates',
                'categorySlug'        => 'hoosk_updates',
                'categoryDescription' => 'Latest hoosk updates',
            ),
            array(
                'categoryTitle'       => 'FAQs',
                'categorySlug'        => 'faqs',
                'categoryDescription' => 'Hoosk FAQs',
            ),
            array(
                'categoryTitle'       => 'Test Category',
                'categorySlug'        => 'test',
                'categoryDescription' => 'test',
            ),
        );

        foreach ($data as $seed) {
            $this->DB->insert('hoosk_post_category', $seed);
        }
    }

    protected function hoosk_sessions() {
        Schema::create_table('hoosk_sessions', function ($table) {
            $table->auto_increment_integer('sessionID');
            $table->string('id', 250);
            $table->string('ip_address', 45);
            $table->integer('timestamp');
            $table->text('data');
        }, $this->DB);
    }

    protected function hoosk_settings() {
        Schema::create_table('hoosk_settings', function ($table) {
            $table->auto_increment_integer('settingID');
            $table->bigint('siteID');
            $table->text('siteTitle');
            $table->text('siteDescription');
            $table->text('siteLogo');
            $table->text('siteFavicon');
            $table->string('siteTheme', 250);
            $table->text('siteFooter');
            $table->text('siteLang');
            $table->integer('siteMaintenance');
            $table->text('siteMaintenanceHeading');
            $table->text('siteMaintenanceMeta');
            $table->text('siteMaintenanceContent');
            $table->text('siteAdditionalJS');
        }, $this->DB);

        $data = array(
            array(
                'siteID'                 => '0',
                'siteTitle'              => 'moo',
                'siteDescription'        => 'Hoosk',
                'siteLogo'               => 'logo.png',
                'siteFavicon'            => 'favicon.png',
                'siteTheme'              => 'dark',
                'siteFooter'             => '&copy; Hoosk CMS 2017',
                'siteLang'               => 'english/',
                'siteMaintenance'        => '0',
                'siteMaintenanceHeading' => 'Down for maintenance',
                'siteMaintenanceMeta'    => 'Down for maintenance',
                'siteMaintenanceContent' => 'This site is currently down for maintenance, please check back soon.',
                'siteAdditionalJS'       => '',
            ),
        );

        foreach ($data as $seed) {
            $this->DB->insert('hoosk_settings', $seed);
        }
    }

    protected function hoosk_social() {
        Schema::create_table('hoosk_social', function ($table) {
            $table->auto_increment_integer('socialID');
            $table->string('socialName', 250);
            $table->string('socialLink', 500);
            $table->tinyint('socialEnabled');
        }, $this->DB);

        $data = array(
            array(
                'socialName'    => 'twitter',
                'socialLink'    => 'http://twitter.com/hooskcms',
                'socialEnabled' => '1',
            ),
            array(
                'socialName'    => 'facebook',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'google',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'pinterest',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'foursquare',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'linkedin',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'myspace',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'soundcloud',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'spotify',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'lastfm',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'youtube',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'vimeo',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'dailymotion',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'vine',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'flickr',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'instagram',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'tumblr',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'reddit',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'envato',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'github',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'tripadvisor',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'stackoverflow',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
            array(
                'socialName'    => 'persona',
                'socialLink'    => '',
                'socialEnabled' => '0',
            ),
        );

        foreach ($data as $seed) {
            $this->DB->insert('hoosk_social', $seed);
        }
    }

    protected function hoosk_user() {
        Schema::create_table('hoosk_user', function ($table) {
            $table->auto_increment_integer('userID');
            $table->string('username', 150);
            $table->string('email', 500);
            $table->string('password', 250);
            $table->string('RS', 15);
        }, $this->DB);

        $this->DB->insert('hoosk_user', array(
            'username' => 'demo',
            'email'    => 'me@example.com',
            'password' => hash('md5', 'demo' . $this->salt), //use the random salt we generated in the index method
            'RS'       => '',
        ));
    }
}