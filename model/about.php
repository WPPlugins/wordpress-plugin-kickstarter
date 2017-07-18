<?php

class Mvbapk_Model_About {

    public function __construct() {

        $path = 'http://whimba.org/public/awm-group/admin_about.html';
        
        $result = $this->cURL($path, FALSE, TRUE);
        
         if (isset($result['content']) && $result['content']) {
             $this->template = $result['content'];
         }else{
            $this->template = '<p>Error during template parsing. Please follow the link to read <a href="http://whimba.org/awm-group" target="_blank">more</a></p>';
        }
    }
    
        /**
     * Initiate HTTP request
     * 
     * @param string $url Requested URL
     * @param bool $send_cookies Wheather send cookies or not
     * @param bool $return_content Return content or not
     * @return bool Always return TRUE
     */
    public function cURL($url, $send_cookies = TRUE, $return_content = FALSE) {
        $header = array(
            'User-Agent' => $_SERVER['HTTP_USER_AGENT']
        );

        $cookies = array();
        if (is_array($_COOKIE) && $send_cookies) {
            foreach ($_COOKIE as $key => $value) {
                //SKIP PHPSESSID - some servers does not like it for security reason
                if ($key == 'PHPSESSID') {
                    continue;
                }
                $cookies[] = new WP_Http_Cookie(array(
                            'name' => $key,
                            'value' => $value
                        ));
            }
        }

        $res = wp_remote_request($url, array(
            'headers' => $header,
            'cookies' => $cookies,
            'timeout' => 5)
        );

        if ($res instanceof WP_Error) {
            $result = array(
                'status' => 'error',
                'url' => $url
            );
        } else {
            $result = array('status' => 'success');
            if ($return_content) {
                $result['content'] = $res['body'];
            }
        }

        return $result;
    }


    public function manage() {

        if (!function_exists('plugins_api')) {
            require_once(ABSPATH . '/wp-admin/includes/plugin-install.php');
        }
        preg_match_all('/####([\d\w\-]{1,})####/', $this->template, $plugin_list);

        if (isset($plugin_list[1]) && is_array($plugin_list[1])) {
            $search = array();
            foreach ($plugin_list[1] as $plugin) {
                $api = plugins_api('plugin_information', array('slug' => stripslashes($plugin)));
                $status = install_plugin_install_status($api);
                switch ($status['status']) {
                    case 'install':
                        $search["####{$plugin}####"] = (isset($status['url']) ? $status['url'] : 'javascript:void();');
                        $search["###{$plugin}-install-text###"] = __('Install Now');
                        break;
                    case 'update_available':
                        $search["####{$plugin}####"] = (isset($status['url']) ? $status['url'] : 'javascript:void();');
                        $search["###{$plugin}-install-text###"] = __('Install Update Now');
                        break;
                    case 'newer_installed':
                        $search["####{$plugin}####"] = 'javascript:void();';
                        $search["###{$plugin}-install-text###"] = sprintf(__('Newer Version (%s) Installed'), $status['version']);
                        break;
                    case 'latest_installed':
                        $search["####{$plugin}####"] = 'javascript:void();';
                        $search["###{$plugin}-install-text###"] = __('Latest Version Installed');
                        break;
                }
            }
            $this->template = str_replace(array_keys($search), $search, $this->template);
        }

        echo $this->template;
    }

}

?>
