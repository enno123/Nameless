<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0 pre-13
 *
 *  Content hooks
 */

class ContentHook extends HookBase {

    public static function codeTransform(array $params = []): array {
        if (parent::validateParams($params, ['content'])) {
            $domDocument = new DOMDocument();
            $domDocument->loadHTML(mb_convert_encoding($params['content'], 'HTML-ENTITIES', 'UTF-8'));
            echo $domDocument->nodeValue;
            $tags = $domDocument->getElementsByTagName('code');
            foreach ($tags as $tag) {
                $code = '';
                $i = 1;
                foreach ($tag->childNodes as $child) {
                    $toAppend = Output::getClean($domDocument->saveHTML($child));

                    // </code> doesn't always get stripped for some reason
                    if ($i === $tag->childNodes->length && substr_compare($toAppend, '&lt;/code&gt;', -13, 13) === 0) {
                        $toAppend = substr($toAppend, 0, -13);
                    }

                    $code .= $toAppend;
                    $i++;
                }

                $tag->nodeValue = $code;
            }

            $params['content'] = $domDocument->saveHTML();
        }

        return $params;
    }

    public static function decode(array $params = []): array {
        if (parent::validateParams($params, ['content'])) {
            $params['content'] = Output::getDecoded($params['content']);
        }

        return $params;
    }

    public static function purify(array $params = []): array {
        if (parent::validateParams($params, ['content'])) {
            $params['content'] = Output::getPurified($params['content'], true);
        }

        return $params;
    }

    public static function renderEmojis(array $params = []): array {
        if (parent::validateParams($params, ['content'])) {
            $params['content'] = Util::renderEmojis($params['content']);
        }

        return $params;
    }

    public static function replaceAnchors(array $params = []): array {
        if (parent::validateParams($params, ['content'])) {
            $params['content'] = Util::replaceAnchorsWithText($params['content']);
        }

        return $params;
    }
}
