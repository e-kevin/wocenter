<?php

namespace wocenter\helpers;

use wocenter\enums\Enums;
use Yii;
use yii\helpers\Html;

/**
 * 字符串助手类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class StringHelper
{
    
    /**
     * 字符串转换为数组，主要用于把分隔符调整到第二个参数
     *
     * @param string $str 要分割的字符串
     * @param string $glue 分割符
     *
     * @return array
     */
    public static function stringToArray($str, $glue = ',')
    {
        return explode($glue, $str);
    }
    
    /**
     * 转换数字ID和字符串形式ID串为数组，并过滤重复和空的数据
     *
     * @param mixed $ids 要格式化的字符串
     *
     * @return array
     */
    public static function parseIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        
        // 转换数字ID和字符串形式ID串为数组
        if (is_numeric($ids)) {
            $ids = [$ids];
        } elseif (is_string($ids)) {
            $ids = self::stringToArray($ids);
        }
        
        $id_array = array_unique(array_filter(array_map('intval', $ids)));
        
        return 0 == count($id_array) ? [] : $id_array;
    }
    
    /**
     * 解析[下拉框,单选框,多选框]类型额外配置值
     * 格式:
     *  1. 以英文逗号分隔。key1:value1,key2:value2,
     *  2. 以英文分号分隔。key1:value1;key2:value2;
     *  3. 以回车换行分隔。
     *      key1:value1
     *      key2:value2
     *
     * @param string $string 要解析的字符串
     *
     * @return array
     */
    public static function parseString($string)
    {
        $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
        if (strpos($string, ':')) {
            $value = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }
        
        return $value;
    }
    
    /**
     * 获取字符串首字母
     *
     * @param $str
     *
     * @return string
     */
    public static function getFirstLetter($str)
    {
        $firstCharOrd = ord(strtoupper($str{0}));
        if ($firstCharOrd >= 65 and $firstCharOrd <= 91) {
            return strtoupper($str{0});
        }
        if ($firstCharOrd >= 48 and $firstCharOrd <= 57) {
            return '#';
        }
        $s = iconv("UTF-8", "gb2312", $str);
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 and $asc <= -20284)
            return "A";
        if ($asc >= -20283 and $asc <= -19776)
            return "B";
        if ($asc >= -19775 and $asc <= -19219)
            return "C";
        if ($asc >= -19218 and $asc <= -18711)
            return "D";
        if ($asc >= -18710 and $asc <= -18527)
            return "E";
        if ($asc >= -18526 and $asc <= -18240)
            return "F";
        if ($asc >= -18239 and $asc <= -17923)
            return "G";
        if ($asc >= -17922 and $asc <= -17418)
            return "H";
        if ($asc >= -17417 and $asc <= -16475)
            return "J";
        if ($asc >= -16474 and $asc <= -16213)
            return "K";
        if ($asc >= -16212 and $asc <= -15641)
            return "L";
        if ($asc >= -15640 and $asc <= -15166)
            return "M";
        if ($asc >= -15165 and $asc <= -14923)
            return "N";
        if ($asc >= -14922 and $asc <= -14915)
            return "O";
        if ($asc >= -14914 and $asc <= -14631)
            return "P";
        if ($asc >= -14630 and $asc <= -14150)
            return "Q";
        if ($asc >= -14149 and $asc <= -14091)
            return "R";
        if ($asc >= -14090 and $asc <= -13319)
            return "S";
        if ($asc >= -13318 and $asc <= -12839)
            return "T";
        if ($asc >= -12838 and $asc <= -12557)
            return "W";
        if ($asc >= -12556 and $asc <= -11848)
            return "X";
        if ($asc >= -11847 and $asc <= -11056)
            return "Y";
        if ($asc >= -11055 and $asc <= -10247)
            return "Z";
        
        return '#';
    }
    
    /**
     * 截取中文字符串
     *
     * @param string $str 需要截取的字符串
     * @param integer $start 开始查询的下标
     * @param integer $length 截取长度
     * @param string $charset 字符串编码
     * @param boolean $suffix 超出部分是否显示省略号
     *
     * @return string
     */
    public static function mbSubstr($str, $start = 0, $length = 5, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $charset);
        } elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
            $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
            $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
            $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        $fix = '';
        if (strlen($slice) < strlen($str)) {
            $fix = '...';
        }
        
        return $suffix ? $slice . $fix : $slice;
    }
    
    /**
     * 检查字符串是否是UTF8编码
     *
     * @param string $str 字符串
     *
     * @return Boolean
     */
    public static function isUtf8($str)
    {
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($str[$i]);
            if ($c > 128) {
                if (($c >= 254))
                    return false;
                elseif ($c >= 252)
                    $bits = 6;
                elseif ($c >= 248)
                    $bits = 5;
                elseif ($c >= 240)
                    $bits = 4;
                elseif ($c >= 224)
                    $bits = 3;
                elseif ($c >= 192)
                    $bits = 2;
                else
                    return false;
                if (($i + $bits) > $len)
                    return false;
                while ($bits > 1) {
                    $i++;
                    $b = ord($str[$i]);
                    if ($b < 128 || $b > 191)
                        return false;
                    $bits--;
                }
            }
        }
        
        return true;
    }
    
    /**
     * 产生随机字串，可用来自动生成密码
     * 默认长度6位 字母和数字混合 支持中文
     *
     * @param integer $len 长度
     * @param integer $type 字串类型，默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
     * 0 - 所有字母，包括大小写
     * 1 - 数字
     * 2 - 大写字母
     * 3 - 小写字母
     * 4 - 中文
     * 默认 - 混合，默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
     * @param string $addChars 额外字符
     *
     * @return string
     */
    public static function randString($len = 6, $type = Enums::UNLIMITED, $addChars = '')
    {
        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 4:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
                break;
            default :
                // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($len > 10) {//位数过长重复字符串一定次数
            $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }
        if ($type != 4) {
            $chars = str_shuffle($chars);
            $str = substr($chars, 0, $len);
        } else {
            // 中文随机字
            for ($i = 0; $i < $len; $i++) {
                $str .= self::mbSubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1, 'utf-8', false);
            }
        }
        
        return $str;
    }
    
    /**
     * 生成一定数量的随机数，并且不重复
     *
     * @param integer $number 数量
     * @param integer $length 长度
     * @param integer $type 字串类型，默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
     * 0 - 所有字母，包括大小写
     * 1 - 数字
     * 2 - 大写字母
     * 3 - 小写字母
     * 4 - 中文
     * 默认 - 混合，默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
     *
     * @return bool|array
     */
    public static function buildCountRand($number, $length = 4, $type = Enums::UNLIMITED)
    {
        if ($type == 1 && $length < strlen($number)) {
            //不足以生成一定数量的不重复数字
            return false;
        }
        $rand = [];
        for ($i = 0; $i < $number; $i++) {
            $rand[] = self::randString($length, $type);
        }
        $unique = array_unique($rand);
        if (count($unique) == count($rand)) {
            return $rand;
        }
        $count = count($rand) - count($unique);
        for ($i = 0; $i < $count * 3; $i++) {
            $rand[] = self::randString($length, $type);
        }
        $rand = array_slice(array_unique($rand), 0, $number);
        
        return $rand;
    }
    
    /**
     * 带格式生成随机字符,支持批量生成,但可能存在重复
     *
     * @param string $format 字符格式
     *  - #:表示数字
     *  - *:表示字母和数字
     *  - $:表示字母
     * @param integer $number 生成数量
     *
     * @return string | array
     */
    public static function buildFormatRand($format, $number = 1)
    {
        $str = [];
        $length = strlen($format);
        $strtemp = '';
        for ($j = 0; $j < $number; $j++) {
            $strtemp = '';
            for ($i = 0; $i < $length; $i++) {
                $char = substr($format, $i, 1);
                switch ($char) {
                    case "*"://字母和数字混合
                        $strtemp .= self::randString(1);
                        break;
                    case "#"://数字
                        $strtemp .= self::randString(1, 1);
                        break;
                    case "$"://大写字母
                        $strtemp .= self::randString(1, 2);
                        break;
                    default://其他格式均不转换
                        $strtemp .= $char;
                        break;
                }
            }
            $str[] = $strtemp;
        }
        
        return $number == 1 ? $strtemp : $str;
    }
    
    /**
     * 获取一定范围内的随机数字 位数不足补零
     *
     * @param integer $min 最小值
     * @param integer $max 最大值
     *
     * @return string
     */
    public static function randNumber($min, $max)
    {
        return sprintf("%0" . strlen($max) . "d", mt_rand($min, $max));
    }
    
    /**
     * 自动转换字符集 支持数组转换
     *
     * @param $string
     * @param string $from
     * @param string $to
     *
     * @return array|string
     */
    public static function autoCharset($string, $from = 'gbk', $to = 'utf-8')
    {
        $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
        $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
        if (strtoupper($from) === strtoupper($to) || empty($string) || (is_scalar($string) && !is_string($string))) {
            //如果编码相同或者非字符串标量则不转换
            return $string;
        }
        if (is_string($string)) {
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($string, $to, $from);
            } elseif (function_exists('iconv')) {
                return iconv($from, $to, $string);
            } else {
                return $string;
            }
        } elseif (is_array($string)) {
            foreach ($string as $key => $val) {
                $_key = self::autoCharset($key, $from, $to);
                $string[$_key] = self::autoCharset($val, $from, $to);
                if ($key != $_key)
                    unset($string[$key]);
            }
            
            return $string;
        } else {
            return $string;
        }
    }
    
    /**
     * 截取含有 html标签的字符串
     *
     * @param string $str 待截取字符串
     * @param int $length 截取长度
     * @param string $url 链接
     * @param string $anchor 截取锚点，如果截取过程中遇到这个标记锚点就截至该锚点处
     *
     * @return string $result 返回值
     * @demo  $res = htmlSubString($str, 256, '...'); //截取256个长度，其余部分用'...'替换
     *
     * @author Wang Jian. <wj@yurendu.com>    Date: 2014/03/16
     */
    public static function htmlSubString($str, $length, $url = null, $anchor = '<!-- break -->')
    {
        $_length = mb_strlen(strip_tags($str), "utf-8"); // 统计字符串长度（中、英文都算一个字符）
        if ($_length <= $length) {
            return $str; // 传入的字符串长度小于截取长度，原样返回
        }
        $strlen_var = strlen($str);  // 统计字符串长度（UTF8编码下-中文算3个字符，英文算一个字符）
        if (strpos($str, '<') === false) {
            return mb_substr($str, 0, $length); // 不包含 html 标签 ，直接截取
        }
        if ($e = strpos($str, $anchor)) {
            return mb_substr($str, 0, $e); // 包含截断标志，优先
        }
        $html_tag = 0;  // html 代码标记 
        $result = '';  // 摘要字符串
        $html_array = ['left' => [], 'right' => []]; //记录截取后字符串内出现的 html 标签，开始=>left,结束=>right
        /*
         * 如字符串为：<h3><p><b>a</b></h3>，假设p未闭合，数组则为：array('left'=>array('h3','p','b'), 'right'=>'b','h3');
         * 仅补全 html 标签，<? <% 等其它语言标记，会产生不可预知结果
         */
        for ($i = 0; $i < $strlen_var; ++$i) {
            if (!$length)
                break; // 遍历完之后跳出
            $current_var = substr($str, $i, 1); // 当前字符
            $html_array_str = '';
            if ($current_var == '<') { // html 代码开始 
                $html_tag = 1;
            } else if ($html_tag == 1) { // 一段 html 代码结束
                if ($current_var == '>') {
                    $html_array_str = trim($html_array_str); //去除首尾空格，如 <br / > < img src="" / > 等可能出现首尾空格
                    if (substr($html_array_str, -1) != '/') { //判断最后一个字符是否为 /，若是，则标签已闭合，不记录
                        // 判断第一个字符是否 /，若是，则放在 right 单元 
                        $f = substr($html_array_str, 0, 1);
                        if ($f == '/') {
                            $html_array['right'][] = str_replace('/', '', $html_array_str); // 去掉 '/' 
                        } else if ($f != '?') { // 若是?，则为 PHP 代码，跳过
                            // 若有半角空格，以空格分割，第一个单元为 html 标签。如：<h2 class="a"> <p class="a"> 
                            if (strpos($html_array_str, ' ') !== false) {
                                // 分割成2个单元，可能有多个空格，如：<h2 class="" id=""> 
                                $html_array['left'][] = strtolower(current(explode(' ', $html_array_str, 2)));
                            } else {
                                //若没有空格，整个字符串为 html 标签，如：<b> <p> 等，统一转换为小写
                                $html_array['left'][] = strtolower($html_array_str);
                            }
                        }
                    }
                    $html_array_str = ''; // 字符串重置
                    $html_tag = 0;
                } else {
                    $html_array_str .= $current_var; //将< >之间的字符组成一个字符串,用于提取 html 标签
                }
            } else {
                --$length; // 非 html 代码才记数
            }
            $ord_var_c = ord($str{$i});
            switch (true) {
                case (($ord_var_c & 0xE0) == 0xC0): // 2 字节 
                    $result .= substr($str, $i, 2);
                    $i += 1;
                    break;
                case (($ord_var_c & 0xF0) == 0xE0): // 3 字节
                    $result .= substr($str, $i, 3);
                    $i += 2;
                    break;
                case (($ord_var_c & 0xF8) == 0xF0): // 4 字节
                    $result .= substr($str, $i, 4);
                    $i += 3;
                    break;
                case (($ord_var_c & 0xFC) == 0xF8): // 5 字节 
                    $result .= substr($str, $i, 5);
                    $i += 4;
                    break;
                case (($ord_var_c & 0xFE) == 0xFC): // 6 字节
                    $result .= substr($str, $i, 6);
                    $i += 5;
                    break;
                default: // 1 字节 
                    $result .= $current_var;
            }
        }
        if ($html_array['left']) { //比对左右 html 标签，不足则补全
            $html_array['left'] = array_reverse($html_array['left']); //翻转left数组，补充的顺序应与 html 出现的顺序相反
            foreach ($html_array['left'] as $index => $tag) {
                $key = array_search($tag, $html_array['right']); // 判断该标签是否出现在 right 中
                if ($key !== false) { // 出现，从 right 中删除该单元
                    unset($html_array['right'][$key]);
                } else { // 没有出现，需要补全 
                    $result .= '</' . $tag . '>';
                }
            }
        }
        if ($url == null) {
            return $result . '...';
        } else {
            $replace = '<br />' . Html::a('<i class="glyphicon glyphicon-hand-right"></i>' . Yii::t('app', 'Unfinished,continue reading') . '>>', $url);
            
            return $result . '...' . $replace;
        }
    }
    
    /**
     * 命名空间转换为实际路径
     *
     * @param string $namespace
     * @param bool $throwException
     *
     * @return bool|string
     */
    public static function ns2Path($namespace, $throwException = true)
    {
        return Yii::getAlias('@' . str_replace('\\', '/', $namespace), $throwException);
    }
    
    /**
     * 替换第一个被找到的字符串
     *
     * @param string $str
     * @param string $find
     * @param string $replace
     *
     * @return string
     */
    public static function replace(string $str, string $find, $replace = ''): string
    {
        return ($position = strpos($str, $find)) !== false ? substr_replace($str, $replace, $position, strlen($find)) : $str;
    }
    
}
