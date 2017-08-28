<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_hoosk_post extends CI_Migration {
	protected $table = 'hoosk_post';

	public function up() {
		Schema::create_table($this->table,function($table) {
			$table->auto_increment_integer('postID');
			$table->text('postURL');
			$table->text('postTitle');
			$table->text('postExcerpt');
			$table->text('postContentHTML');
			$table->text('postContent');
			$table->text('postImage');
			$table->integer('categoryID');
			$table->integer('published');
			$table->string('datePosted',100);
			$table->integer('unixStamp');
		});

		$this->seed();
	}

	public function down() {
		$this->dbforge->drop_table($this->table);
	}

	protected function seed() {
		$this->db->insert($this->table,array(
			'postURL' => 'hello_hoosk',
			'postTitle' => 'Hello Hoosk.',
			'postExcerpt' => 'Brain freeze. Kinda hot in these rhinos. Here she comes to wreck the day. Brain freeze. Excuse me, I\'d like to ASS you a few questions.',
			'postContentHTML' => '<div class=\'row\'><div class=\'col-md-6\'><p>Brain freeze. Kinda hot in these rhinos. Here she comes to wreck the day. Brain freeze. Excuse me, I\'d like to ASS you a few questions. We\'re going for a ride on the information super highway. Your entrance was good, his was better. Kinda hot in these rhinos. It\'s because i\'m green isn\'t it! Here she comes to wreck the day. Alrighty Then Excuse me, I\'d like to ASS you a few questions. </p>
 <a href="www.google.com" class="btn btn-default ">Button</a>
 </div><div class=\'col-md-6\'><p>Your entrance was good, his was better. We got no food we got no money and our pets heads are falling off! Haaaaaaarry. Look at that, it\'s exactly three seconds before I honk your nose and pull your underwear over your head. It\'s because i\'m green isn\'t it! Hey, maybe I will give you a call sometime. Your number still 911? Excuse me, I\'d like to ASS you a few questions. </p>
 </div></div>',
			'postContent' => '{"data":[{"type":"columns","data":{"columns":[{"width":6,"blocks":[{"type":"text","data":{"text":"Brain freeze. Kinda hot in these rhinos. Here she comes to wreck the day. Brain freeze. Excuse me, I\'d like to ASS you a few questions. We\'re going for a ride on the information super highway. Your entrance was good, his was better. Kinda hot in these rhinos. It\'s because i\'m green isn\'t it! Here she comes to wreck the day. Alrighty Then Excuse me, I\'d like to ASS you a few questions. \n"}},{"type":"button","data":{"size":"","style":"btn-default","is_block":false,"url":"www.google.com","null":"0","html":"Button"}}]},{"width":6,"blocks":[{"type":"text","data":{"text":"Your entrance was good, his was better. We got no food we got no money and our pets heads are falling off! Haaaaaaarry. Look at that, it\'s exactly three seconds before I honk your nose and pull your underwear over your head. It\'s because i\'m green isn\'t it! Hey, maybe I will give you a call sometime. Your number still 911? Excuse me, I\'d like to ASS you a few questions. \n"}}]}],"preset":"columns-6-6"}}]}',
			'postImage' => 'large_logo.png',
			'categoryID' => '1',
			'published' => '0',
			'datePosted' => date('Y-m-d H:i:s'),
			'unixStamp' => time()
		));

		$this->db->insert($this->table,array(
			'postURL' => 'me_im_dishonest',
			'postTitle' => 'Me? I\'m dishonest',
			'postExcerpt' => 'A drug person can learn to cope with things like seeing their dead grandmother crawling up their leg with a knife in her teeth. But no one should be asked to handle this trip. Well, then, I confess, it is my intention to commandeer one of these ships, pick up a crew in Tortuga, raid, pillage, plunder and otherwise pilfer my weasely black guts out.',
			'postContentHTML' => '',
			'postContent' => '',
			'postImage' => 'responsive_hoosk.png',
			'categoryID' => '3',
			'published' => '1',
			'datePosted' => date('Y-m-d H:i:s'),
			'unixStamp' => time()
		));

		$this->db->insert($this->table,array(
			'postURL' => 'yes_i_used_a_machine_gun',
			'postTitle' => 'Yes, I used a machine gun.',
			'postExcerpt' => 'You wouldn\'t hit a man with no trousers on, would you? You\'re only supposed to blow the bloody doors off! You know, your bobby dangler, giggle stick, your general-two-colonels, master of ceremonies... Yeah,',
			'postContentHTML' => '<div class=\'row\'><div class=\'col-md-6\'><p>You\'re only supposed to blow the bloody doors off! Jasper: Your baby is the miracle the whole world has been waiting for. Yes, I used a machine gun. You know, your bobby dangler, giggle stick, your general&#45;two&#45;colonels, master of ceremonies... Yeah, don\'t be shy, let\'s have a look. My lord! You\'re a tripod. My lord! You\'re a tripod. I took a Viagra, got stuck in me throat, I\'ve had a stiff neck for hours. When I get back, remind to tell you about the time I took 100 nuns to Nairobi! At this point, I\'d set you up with a chimpanzee if it\'d brought you back to the world! Pull my finger! It\'s not the size mate, it\'s how you use it. You wouldn\'t hit a man with no trousers on, would you? </p>
 </div><div class=\'col-md-6\'><p>Your were only supposed to blow the bloody doors off. My lord! You\'re a tripod. When I get back, remind to tell you about the time I took 100 nuns to Nairobi! It\'s not the size mate, it\'s how you use it. At this point, I\'d set you up with a chimpanzee if it\'d brought you back to the world! </p>
 <>Hola Mundo!!!</></div></div>',
			'postContent' => '{"data":[{"type":"columns","data":{"columns":[{"width":6,"blocks":[{"type":"text","data":{"text":" You\'re only supposed to blow the bloody doors off! Jasper: Your baby is the miracle the whole world has been waiting for. Yes, I used a machine gun. You know, your bobby dangler, giggle stick, your general\\-two\\-colonels, master of ceremonies... Yeah, don\'t be shy, let\'s have a look. My lord! You\'re a tripod. My lord! You\'re a tripod. I took a Viagra, got stuck in me throat, I\'ve had a stiff neck for hours. When I get back, remind to tell you about the time I took 100 nuns to Nairobi! At this point, I\'d set you up with a chimpanzee if it\'d brought you back to the world! Pull my finger! It\'s not the size mate, it\'s how you use it. You wouldn\'t hit a man with no trousers on, would you? \n\n"}}]},{"width":6,"blocks":[{"type":"text","data":{"text":"Your were only supposed to blow the bloody doors off. My lord! You\'re a tripod. When I get back, remind to tell you about the time I took 100 nuns to Nairobi! It\'s not the size mate, it\'s how you use it. At this point, I\'d set you up with a chimpanzee if it\'d brought you back to the world! \n"}},{"type":"heading","data":{"text":"Hola Mundo!!!","heading":""}}]}],"preset":"columns-6-6"}}]}',
			'postImage' => 'jumbotron.jpg',
			'categoryID' => '4',
			'published' => '1',
			'datePosted' => date('Y-m-d H:i:s'),
			'unixStamp' => time()
		));
	}
}