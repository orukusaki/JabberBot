<?php
class TwitterTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideTweetData
     *
     * @param unknown_type $search
     * @param unknown_type $return
     * @param unknown_type $expectedURI
     * @param unknown_type $expectedReply
     */
    public function testGetTweet($search, $return, $expectedURI, $expectedReply)
    {
        $message = $this->getMock(
            'JabberBot_Message',
            array('reply', 'getUsername'),
            array(),
            '',
            false    // disable constructor
        );

        $message->expects($this->any())
                ->method('getUsername')
                ->will($this->returnValue('test'));

        $message->body = '*twitter '. $search;

        $message->expects($this->once())
                ->method('reply')
                ->with($expectedReply);

        $command = $this->getMock(
            'JabberBot_Command_Twitter',
            array('curlGet', 'checkAcl'),
            array(),
            '',
            false    // disable constructor
        );

        $command->expects($this->once())
                ->method('curlGet')
                ->with($expectedURI)
                ->will($this->returnValue($return));

        $command->run($message);
    }

    public function provideTweetData()
    {
        $tweets = array(
            array(
                'search'        => 'orukusaki',
                'return'        => '{"contributors_enabled":false,"protected":false,"location":"Sheffield","default_profile_image":false,"friends_count":77,"profile_background_color":"131516","profile_image_url_https":"https:\/\/si0.twimg.com\/profile_images\/1442622045\/profile__11__normal.jpg","name":"Peter Smith","profile_background_tile":true,"profile_background_image_url_https":"https:\/\/si0.twimg.com\/images\/themes\/theme14\/bg.gif","favourites_count":0,"followers_count":32,"url":null,"profile_image_url":"http:\/\/a3.twimg.com\/profile_images\/1442622045\/profile__11__normal.jpg","is_translator":false,"show_all_inline_media":false,"utc_offset":null,"profile_sidebar_fill_color":"efefef","description":"PHP Developer\nAmateur Musician\nWearer of Hats","lang":"en","time_zone":null,"profile_sidebar_border_color":"eeeeee","id_str":"47113465","status":{"coordinates":null,"contributors":null,"retweet_count":1,"in_reply_to_user_id":null,"favorited":false,"geo":null,"in_reply_to_screen_name":null,"id_str":"162581097421619203","place":null,"retweeted":false,"in_reply_to_status_id":null,"created_at":"Thu Jan 26 17:02:08 +0000 2012","in_reply_to_status_id_str":null,"truncated":false,"source":"\u003Ca href=\"http:\/\/itunes.apple.com\/us\/app\/twitter\/id409789998?mt=12\" rel=\"nofollow\"\u003ETwitter for Mac\u003C\/a\u003E","in_reply_to_user_id_str":null,"id":162581097421619203,"text":"30 years old and finally no longer a junior."},"listed_count":1,"geo_enabled":false,"created_at":"Sun Jun 14 15:45:13 +0000 2009","screen_name":"orukusaki","verified":false,"profile_use_background_image":true,"default_profile":false,"notifications":null,"profile_text_color":"333333","follow_request_sent":null,"statuses_count":77,"profile_background_image_url":"http:\/\/a1.twimg.com\/images\/themes\/theme14\/bg.gif","id":47113465,"following":null,"profile_link_color":"009999"}',
                'expectedURI'   => 'http://api.twitter.com/1/users/show.json?include_entities=false&screen_name=orukusaki',
                'expectedReply' => 'Update from Peter Smith at Thu Jan 26 17:02:08 +0000 2012:
30 years old and finally no longer a junior.',
            ),
            array(
                'search'        => '163339219392139264',
                'return'        => '{"coordinates":null,"contributors":null,"retweet_count":1,"in_reply_to_user_id":null,"favorited":false,"geo":null,"in_reply_to_screen_name":null,"user":{"contributors_enabled":false,"protected":false,"location":"Norf London","default_profile_image":false,"friends_count":202,"profile_background_color":"ffffff","profile_image_url_https":"https:\/\/si0.twimg.com\/profile_images\/1534159164\/rob2_normal.gif","name":"Rob Manuel","profile_background_tile":true,"profile_background_image_url_https":"https:\/\/si0.twimg.com\/profile_background_images\/212703456\/twilk_background_4d701bfed7c44.jpg","favourites_count":1,"followers_count":9839,"url":"http:\/\/www.robmanuel.com\/","profile_image_url":"http:\/\/a1.twimg.com\/profile_images\/1534159164\/rob2_normal.gif","is_translator":false,"show_all_inline_media":true,"utc_offset":0,"profile_sidebar_fill_color":"ddd8fc","description":"Co-founded B3ta. Ginger. Likes cheese.","lang":"en","time_zone":"London","profile_sidebar_border_color":"a6ff00","id_str":"2285051","listed_count":353,"geo_enabled":false,"created_at":"Mon Mar 26 11:48:34 +0000 2007","screen_name":"robmanuel","verified":false,"profile_use_background_image":true,"default_profile":false,"notifications":false,"profile_text_color":"000000","follow_request_sent":false,"statuses_count":17967,"profile_background_image_url":"http:\/\/a3.twimg.com\/profile_background_images\/212703456\/twilk_background_4d701bfed7c44.jpg","id":2285051,"following":true,"profile_link_color":"ff0000"},"id_str":"163339219392139264","place":null,"retweeted":false,"in_reply_to_status_id":null,"created_at":"Sat Jan 28 19:14:39 +0000 2012","in_reply_to_status_id_str":null,"truncated":false,"source":"web","in_reply_to_user_id_str":null,"id":163339219392139264,"text":"Science should genetically engineer women to lay eggs. It would solve any birth phobias & Dad could sit on nest whilst mum looks for worms."}',
                'expectedURI'   => 'http://api.twitter.com/1/statuses/show/163339219392139264.json?include_entities=false',
                'expectedReply' => 'Update from Rob Manuel at Sat Jan 28 19:14:39 +0000 2012:
Science should genetically engineer women to lay eggs. It would solve any birth phobias & Dad could sit on nest whilst mum looks for worms.',
            )
        );

        return $tweets;
    }

}
