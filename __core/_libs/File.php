<?php

/**
 * Description of File
 *
 * @author pangyiguang
 */
class File {

    private $_DirHandle = null;
    private $_CurrentDir = null;
    private $_CurrentFile = null;

    function __construct($init = array()) {
        try {
            if ($init && is_array($init)) {
                if (isset($init['dir'])) {
                    $this->_CurrentDir = realpath($init['dir']);
                    if (!$this->_dirIsExist()) {
                        throw new Exception($this->_CurrentDir . ' 目录不存在');
                    }
                }
                if (isset($init['file'])) {
                    $this->_CurrentFile = $init['file'];
                    if (!$this->_fileIsExist()) {
                        throw new Exception($this->_CurrentFile . ' 文件不存在');
                    }
                }
            }
        } catch (Exception $exc) {
            Common::output($exc->getMessage());
        }
    }

    private function _readDir() {
        $this->_DirHandle = dir($this->_CurrentDir);
    }

    private function _fileIsExist() {
        if (is_file($this->_CurrentFile) && file_exists($this->_CurrentFile)) {
            return true;
        }
        return false;
    }

    private function _dirIsExist() {
        if (is_dir($this->_CurrentFile) && file_exists($this->_CurrentFile)) {
            return true;
        }
        return false;
    }

    public function echoCurrentDirTree($originDirectory, $printDistance = 0) {
        if ($printDistance == 0) {
            echo '<div style="color:#35a; font-family:Verdana; font-size:11px;">';
        }
        $leftWhiteSpace = '';
        for ($i = 0; $i < $printDistance; $i++) {
            $leftWhiteSpace = $leftWhiteSpace . '&nbsp;';
        }

        $CurrentWorkingDirectory = dir($originDirectory);
        while ($entry = $CurrentWorkingDirectory->read()) {
            if ($entry != '.' && $entry != '..') {
                if (is_dir($originDirectory . '\\' . $entry)) {
                    echo $leftWhiteSpace . '<b>' . $entry . '</b><br />';
                    printCurrentDirRecursively($originDirectory . '\\' . $entry, $printDistance + 2);
                } else {
                    echo $leftWhiteSpace . $entry . '<br />';
                }
            }
        }
        $CurrentWorkingDirectory->close();
        if ($printDistance == 0) {
            echo '</div>';
        }
    }

}
