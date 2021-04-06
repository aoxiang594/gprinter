# 佳博云打印


我是用来`打印标签`(不是票据、不是票据、不是票据)

所以这里只是做了打印标签的接口

官方有提供简单的 SDK 供参考

SDK 下载[http://poscom.cn/index.php?catid=18](http://poscom.cn/index.php?catid=18)




```
use  Aoxiang\Gprinter\Client;

$apiKey     = '';
$memberCode = '';
$deviceId   = '';
$client     = new Client($apiKey, $memberCode);
$template   = 'SIZE 30 mm,320 mm
GAP 2 mm,0 mm
REFERENCE 0,0
SPEED 2
DENSITY 8
DIRECTION 0
SET HEAD ON
SET PRINTKEY OFF
SET KEY1 ON
SET KEY2 ON
SHIFT 0
CLS
QRCODE 25,25,L,3,A,0,"https://mp.weixin.qq.com/a/~~4Sl9qv7xeEk~Yo-_mJGLfwkBcz25YBipuA~~"
TEXT 240,25,"TSS24.BF2",90,1,1,"劳力士116518"
TEXT 203,25,"TSS24.BF2",90,1,1,"编号 A41FFD1"
TEXT 165,25,"TSS24.BF2",90,1,1,"入库日期 2021年04月02日"
TEXT 80,160,"TSS24.BF2",90,1,1,"售价 ￥25,000"
PRINT 1,1
';
$client->setDeviceId($deviceId)->tag($template);


```
