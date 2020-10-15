<?php
class App_Helpers_LastVisited {
    /**
     * Example use:
     * App_Helpers_LastVisited::saveThis($this->_request->getRequestUri());
     */
    public static function saveThis($url) {
        $lastPg = new Zend_Session_Namespace('history');
        $lastPg->last = $url;
        //echo $lastPg->last;// results in /controller/action/param/foo
    }

    /**
     * I typically use redirect:
     * $this->_redirect(App_Helpers_LastVisited::getLastVisited());
     */
    public static function getLastVisited() {
        $lastPg = new Zend_Session_Namespace('history');
        if(!empty($lastPg->last)) {
            $path = $lastPg->last;
            $lastPg->unsetAll();
            return $path;
        }

        return ''; // Go back to index/index by default;
     }
}
?>