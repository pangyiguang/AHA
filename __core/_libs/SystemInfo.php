<?php

/**
 * Description of SystemInfo
 *
 * @author pangyiguang
 */
class SystemInfo {

    /**
     * @todo 获取磁盘列表
     *
     * @return array | bool 
     */
    static function GetDiskList() {
        if (strpos(PHP_OS, 'WIN') === false) {
            return false;
        }
        $disks = range('c', 'w');
        foreach ($disks as $disk) {
            $disk = $disk . ":";
            if (is_dir($disk) !== false && disk_total_space($disk) > 0) {
                $disk_list[] = $disk;
            }
        }
        return $disk_list;
    }

    /**
     * @todo 获取磁盘空间信息
     * @param string $disk_name     盘符标识
     * @param boolen $convert_size  是否转化单位
     * @return array | boolen 
     */
    static function GetDiskSpace($disk_name, $convert_size = false) {
        if (is_dir($disk_name) === false) {
            return false;
        }
        $disk_space['total'] = (float) disk_total_space($disk_name);
        $disk_space['free'] = (float) disk_free_space($disk_name);
        $disk_space['used'] = $disk_space['total'] - $disk_space['free'];
        $disk_space['percent'] = (float) round($disk_space['used'] / $disk_space['total'] * 100);
        if ($convert_size === false) {
            return $disk_space;
        }
        $disk_space['total'] = Common::SizeConvert($disk_space['total']);
        $disk_space['free'] = Common::SizeConvert($disk_space['free']);
        $disk_space['used'] = Common::SizeConvert($disk_space['used']);
        $disk_space['percent'] = $disk_space['percent'] . '%';
        return $disk_space;
    }

}