<?php
require_once(__DIR__ . "/../lib/emoji/emoji.php");

class NInsta_TextHandler {
    private $urlHandler;
    private $tagsRegex = '~#(\w+)~iu';
    //private $mentionsRegex = '~@([a-zA-Z\.\_\-0-9]([^.]*)+)~iu';
    // private $mentionsRegex = '~@([^\s]+(?<![^a-z0-9A-Z\_]))~iu';
    private $mentionsRegex = '~@([a-zA-Z0-9\_\.]+(?<![^a-z0-9A-Z\_]))~iu';

    function __construct($urlHandler) {
        $this->urlHandler = $urlHandler;
    }

    public function parseTagsFrom($text) {
        preg_match_all($this->tagsRegex, $text, $matches);

        return $matches[1];
    }

    public function parseUsernameFrom($text) {
        preg_match_all($this->mentionsRegex, $text, $matches);

        return $matches[1];
    }

    private function replaceTagsWithLinks($caption) {
        $caption = preg_replace_callback($this->tagsRegex, function($matches) {
            $tag = $matches[0];
            $tagLink = $this->urlHandler->getTagLink($matches[1]);

            return sprintf('<a href="%s">%s</a>', $tagLink, $tag);
        }, $caption);

        return $caption;
    }

    public function parseMentions($caption) {
        preg_match_all($this->mentionsRegex, $caption, $matches);
        return $matches;
    }

    private function replaceMentionsWithLinks($caption) {
        $pattern = "/(?:[A-Za-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[A-Za-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?\.)+[A-Za-z0-9](?:[A-Za-z0-9-]*[A-Za-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[A-Za-z0-9-]*[A-Za-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";
        preg_match($pattern, $caption, $emails);

        foreach($emails as $email) {
            $newEmail = str_replace('@', 'AT|AT', $email);
            $caption = str_replace($email, $newEmail, $caption);
        }

        $caption = preg_replace_callback($this->mentionsRegex, function($matches) {
            $username = $matches[0];
            $userLink = $this->urlHandler->getUsernameLink($matches[1]);

            return sprintf('<a href="%s">%s</a>', $userLink, $username);
        }, $caption);

        $caption = str_replace('AT|AT', '@', $caption);

        return $caption;
    }

    public function replaceWithEmoji($text) {
        return emoji_unified_to_html($text);
    }

    public function prepareCaption($caption, $maxLength=null) {
        if ($maxLength) {
            $caption = $this->trimCaption($caption, $maxLength);
        }

        $caption = $this->replaceTagsWithLinks($caption);
        $caption = $this->replaceMentionsWithLinks($caption);

        $caption = $this->replaceWithEmoji($caption);

        return $caption;
    }

    public function trimCaption($caption, $maxLength = 80) {
        $splitted = mb_split(" ", $caption);

        $textLength = 0;
        $words = [];

        foreach ($splitted as $word) {
            $textLength += mb_strlen($word);
            $words[] = $word;
            if ($textLength >= $maxLength) {
                break;
            }
        }

        $trimmedCaption = implode(" ", $words);
        if($trimmedCaption !== $caption) {
            $trimmedCaption .= " ...";
        }

        return $trimmedCaption;
    }

    public function dateAgo($date) {
        $timeAgo = new TimeAgo();

        return $timeAgo->inWords(date("r", $date));
    }
}

