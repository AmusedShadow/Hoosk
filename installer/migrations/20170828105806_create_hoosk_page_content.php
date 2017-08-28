<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_page_content extends CI_Migration {
    protected $table = 'hoosk_page_content';

    public function up() {
        Schema::create_table($this->table, function ($table) {
            $table->auto_increment_integer('contentID');
            $table->integer('pageID');
            $table->text('pageTitle');
            $table->text('navTitle');
            $table->text('pageContent');
            $table->text('pageContentHTML');
            $table->text('jumbotron');
            $table->text('jumbotronHTML');
            $table->timestamp('pageCreated');
        });

        $this->seed();
    }

    public function down() {
        $this->dbforge->drop_table($this->table);
    }

    protected function seed() {
        $this->db->insert($this->table, array(
            'pageID'          => 1,
            'pageTitle'       => 'Hoosk Demo',
            'navTitle'        => 'Home',
            'pageContent'     => '{"data":[{"type":"columns","data":{"columns":[{"width":6,"blocks":[{"type":"heading","data":{"text":"This is the Hoosk demo site.\n","heading":""}},{"type":"text","data":{"text":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.\n\n"}},{"type":"text","data":{"text":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortkjor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.\n\n"}}]},{"width":6,"blocks":[{"type":"image_extended","data":{"file":{"url":"http://beta.hoosk.org/images/responsive_hoosk.png","filename":"responsive_hoosk.png"},"caption":"Hoosk is responsive","source":""}}]}],"preset":"columns-6-6"}}]}',
            'pageContentHTML' => '<div class=\'row\'><div class=\'col-md-6\'><>This is the Hoosk demo site.
</><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.</p>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortkjor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.</p>
</div><div class=\'col-md-6\'><img class="img-responsive" src="http://beta.hoosk.org/images/responsive_hoosk.png" alt="Hoosk is responsive" />
</div></div>',
            'jumbotron'       => '{"data":[{"type":"image_extended","data":{"file":{"url":"http://beta.hoosk.org/images/large_logo.png","filename":"large_logo.png"},"caption":"Hoosk Emblem","source":""}},{"type":"image_extended","data":{"file":{"url":"http://beta.hoosk.org/images/welcome_to_hoosk.png","filename":"welcome_to_hoosk.png"},"caption":"welcome to hoosk","source":""}},{"type":"text","data":{"text":"This demo resets every half hour, the login details are:\n\n"}},{"type":"columns","data":{"columns":[{"width":6,"blocks":[{"type":"text","data":{"text":"Username \\- demo\n\n"}}]},{"width":6,"blocks":[{"type":"text","data":{"text":"Password \\- demo\n\n"}}]}],"preset":"columns-6-6"}},{"type":"button","data":{"size":"btn-lg","style":"btn-primary","is_block":false,"url":"/admin","null":"0","html":"Login!"}}]}',
            'jumbotronHTML'   => '<img class="img-responsive" src="http://beta.hoosk.org/images/large_logo.png" alt="Hoosk Emblem" />
<img class="img-responsive" src="http://beta.hoosk.org/images/welcome_to_hoosk.png" alt="welcome to hoosk" />
<p>This demo resets every half hour, the login details are:</p>
<div class=\'row\'><div class=\'col-md-6\'><p>Username &#45; demo</p>
</div><div class=\'col-md-6\'><p>Password &#45; demo</p>
</div></div><a href="/admin" class="btn btn-primary btn-lg">Login!</a>
',
            'pageCreated'     => date('Y-m-d H:i:s'),
        ));

        $this->db->insert($this->table, array(
            'pageID'          => 2,
            'pageTitle'       => 'Contact',
            'navTitle'        => 'Contact',
            'pageContent'     => '{"data":[{"type":"heading","data":{"text":"Contact"}},{"type":"text","data":{"text":"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.\n"}}]}',
            'pageContentHTML' => '<h2>Contact</h2>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quam nisl, sodales id lobortis quis, dapibus quis mauris. Fusce sed placerat risus. Pellentesque imperdiet ex et libero eleifend, ac mattis tortor ultricies. Donec vel ullamcorper purus. Vestibulum dignissim ipsum quis porta finibus.</p>',
            'jumbotron'       => '{"data":[{"type":"image_extended","data":{"file":{"url":"http://beta.hoosk.org/images/large_logo.png","filename":"large_logo.png"},"caption":"Hoosk Emblem","source":""}}]}',
            'jumbotronHTML'   => '<img class="img-responsive" src="http://beta.hoosk.org/images/large_logo.png" alt="Hoosk Emblem" />',
            'pageCreated'     => date('Y-m-d H:i:s'),
        ));

        $this->db->insert($this->table, array(
            'pageID'          => 3,
            'pageTitle'       => 'News',
            'navTitle'        => 'News',
            'pageContent'     => '',
            'pageContentHTML' => '',
            'jumbotron'       => '',
            'jumbotronHTML'   => '',
            'pageCreated'     => date('Y-m-d H:i:s'),
        ));
    }
}