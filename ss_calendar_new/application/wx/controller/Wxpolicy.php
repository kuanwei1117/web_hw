<?php
/**
 * @Author: gunjianpan
 * @Date:   2019-04-18 11:25:17
 * @Last Modified by:   gunjianpan
 * @Last Modified time: 2019-05-16 11:11:16
 */

namespace app\wx\controller;

use app\wx\common\Common;
use app\logmanage\model\Log as LogModel;


class Wxpolicy extends Common
{
    public function getPolicy(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type,X-Requested-With, Content-Type, X-File-Name,token,Access-Control-Allow-Origin,Access-Control-Allow-Methods,Access-Control-Max-Age,authorization");
        $provisionFile = fopen("provisions.txt", "r") or die("Unable to open file!");
        $last = fread($provisionFile, filesize("provisions.txt"));
        fclose($provisionFile);
        return json(['data' => $last, 'code' => 20010]);
    }
}