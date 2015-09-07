<?php

class NInsta_UrlHandler {
    function __construct() {
        $this->mediaPrefix = '/m/';
        $this->categoryPrefix = '/c/';
        $this->tagPrefix = '/t/';
        $this->usernamePrefix = '/';

        $this->mediaEncoder = null;
        $this->mediaDecoder = null;
    }

    public function setMediaPrefix($prefix) {
        $this->mediaPrefix = $prefix;
    }

    public function setCategoryPrefix($prefix) {
        $this->categoryPrefix = $prefix;
    }

    public function setTagPrefix($prefix) {
        $this->tagPrefix = $prefix;
    }

    public function setUsernamePrefix($prefix) {
        $this->usernamePrefix = $prefix;
    }

    public function setMediaEncoder($mediaEncoder) {
        $this->mediaEncoder = $mediaEncoder;
    }

    public function setMediaDecoder($mediaDecoder) {
        $this->mediaDecoder = $mediaDecoder;
    }

    public function getMediaLink($mediaID) {
        if ($this->mediaEncoder) {
            $mediaID = $this->mediaEncoder($mediaID);
        }

        //$encoded = $this->encodeMedia($mediaID);
        return $this->mediaPrefix . $mediaID;
    }

    public function __call($method, $args) {
        if(isset($this->$method) && is_callable($this->$method)) {
            return call_user_func_array(
                $this->$method, $args
            );
        }
    }

    //public function encodeMedia($mediaID) {
    //    list($first, $second) = explode('_', $mediaID);
    //    $first = dechex($first);
    //    $second = dechex($second);
    //    return $first . "_" .$second;
    //}

    public function decodeMedia($mediaID) {
        if ($this->mediaDecoder) {
            $mediaID = $this->mediaDecoder($mediaID);
        }

        return $mediaID;

        //list($first, $second) = explode('_', $mediaID);
        //$first = hexdec($first);
        //$second = hexdec($second);
        return $first . "_" .$second;
    }

    public function getUserSectionLink($section, $maxID=null) {
        $link = '/userpanel.php?section='.$section;

        if ($maxID) {
            $link .= '&maxID='.$maxID;
        }

        return $link;
    }

    public function getCategoryLink($category, $maxID=null) {
        if (!$category){
            $category = "popular";
        }

        $category = strtolower($category);

        $link = $this->categoryPrefix . $category;

        if ($maxID) {
            $link .= "/$maxID";
        }

        return $link;
    }

    public function getTagLink($tag, $maxID=null) {
        $tag = mb_strtolower($tag, "UTF-8");

        $link = $this->tagPrefix . $tag;

        if ($maxID) {
            $link .= "/$maxID";
        }

        return $link;
    }

    public function getUsernameLink($username, $maxID=null) {
        $username = strtolower($username);

        $link = $this->usernamePrefix . $username;

        if ($maxID) {
            $link .= "/$maxID";
        }

        return $link;
    }

    public function getTermsOfUseLink() {
        return '/terms-of-use';
    }

    public function getContactUsLink() {
        return '/contact-us';
    }

    public function getToolsLink() {
        return '/tools';
    }

    public function getLikeUnlikeLink($action, $mediaID) {
        return "/useraction.php?action={$action}&media_id={$mediaID}";
    }

    public function getAuthLink() {
        return '/redirect.php?op=getauth';
    }

    public function getLogoutLink() {
        return '/logout.php';
    }

    public function getFollowLink($userID) {
        return '/useraction.php?action=follow&userID='.$userID;
    }

    public function getUnfollowLink($userID) {
        return '/useraction.php?action=unfollow&userID='.$userID;
    }

    public function getFollowingLink($username, $maxID=null) {
        $url = [];
        $url[] = $username;

        $url[] = 'following';

        if ($maxID) {
            $url[] = $maxID;
        }

        return '/' . implode('/', $url);
    }

    public function getFollowersLink($username, $maxID=null) {
        $url = [];
        $url[] = $username;

        $url[] = 'followers';

        if ($maxID) {
            $url[] = $maxID;
        }

        return '/' . implode('/', $url);
    }
}
