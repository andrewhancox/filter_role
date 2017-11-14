<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    filter_role
 * @copyright  2017 onwards Andrew Hancox (andrewdchancox@googlemail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class filter_role extends moodle_text_filter {
    function filter($text, array $options = array()) {

        if (empty($text) or is_numeric($text)) {
            return $text;
        }

        if (strpos($text, 'hideforrole') === false) { // The regex is pretty gnarly so lets try to skip it if possible.
            return $text;
        }

        $hideforrolesearch = '/<span(\s+role="([a-zA-Z0-9_-]+)"|\s+class="hideforrole"){2}\s*>([\s\S]+?)<\/span>/ims';
        $result = preg_replace_callback($hideforrolesearch, [$this, 'filter_role_hide'], $text);

        $hideforrolesearch = '/<span(\s+role="([a-zA-Z0-9_-]+)"|\s+class="showforrole"){2}\s*>([\s\S]+?)<\/span>/ims';
        $result = preg_replace_callback($hideforrolesearch, [$this, 'filter_role_show'], $result);

        if (is_null($result)) {
            return $text; // Something went wrong when doing regex.
        } else {
            return $result;
        }
    }

    private function hasrole($roleshortname) {
        global $USER;

        $cachekey = $this->context->id . '_' . $USER->id;
        $cache = \cache::make('filter_role', 'userrolesincontext');


        $userrolesshortnames = $cache->get($cachekey);

        if ($userrolesshortnames === false) {
            $userrolesshortnames = [];
            $userroles = get_users_roles($this->context, [$USER->id]);

            if (empty($userroles) || empty([$USER->id])) {
                return false;
            }

            foreach ($userroles[$USER->id] as $userrole) {
                $userrolesshortnames[] = $userrole->shortname;
            }

            $cache->set($cachekey, $userrolesshortnames);
        }

        return in_array($roleshortname, $userrolesshortnames);
    }

    function filter_role_hide($block) {
        $role = $block[2];

        if ($this->hasrole($role)) {
            return '';
        } else {
            return $block[0];
        }
    }

    function filter_role_show($block) {
        $role = $block[2];

        if (!$this->hasrole($role)) {
            return '';
        } else {
            return $block[0];
        }
    }
}


