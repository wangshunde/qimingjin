<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    protected $appid = "wx5cf1906edb367f98";
    protected $appsecret = "72e6b94d1bd25051f0fb81108d2db3c4";
    //微信授权后获取用户信息
    public function index(){
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $code = $_GET["code"];
        $access_token = $this->gettoken($appid,$appsecret);
        $userinfo = $this->getUserInfo($appid,$appsecret,$access_token,$code);
        //信息存入数据库
        $openid=$userinfo['openid'];
        $check = D("user")->where("vc_openid='$openid'")->find();
        if($check==NULL){
            $time = $userinfo['subscribe_time'];
            $info['vc_name'] = $userinfo['nickname'];
            $info['vc_photo'] = $userinfo['headimgurl'];
            $info['vc_openid'] = $openid;
            if($openid){
                $res = D("user")->add($info);
            }
        }else if($check['vc_name']==NULL || $check['vc_name']==''){
            $time = $userinfo['subscribe_time'];
            $info['vc_name'] = $userinfo['nickname'];
            $info['vc_photo'] = $userinfo['headimgurl'];
            $res = D("user")->where("vc_openid='$openid'")->save($info);
        }  
        
        session('openid',$openid);

        $day = date('Y-m-d',time());
        $wherevip['vc_openid'] = $openid;
        $wherevip['bl_vip'] = 1;
        $map['dt_limit'] = array('EGT',$day);
        $res = D("user")->where($wherevip)->where($map)->count();
        if($res==0){
            $vip = 0;
        }else{
            $vip = 1;
        }
        session('vip',$vip);
        //$this->assign('openid',$openid);
        //$this->assign('tittle','电力培训');
        //$this->display();
        $this->redirect('Index/home');
    }

    public function gettoken(){
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
        $token_json = $this->https_request($url);
        $token_arr=json_decode($token_json,true);
        $access_token=$token_arr['access_token'];
        cookie('token',$access_token,5600);
        return $access_token;
    }

    protected function getUserInfo($appid,$appsecret,$access_token,$code){ 
        //oauth2的方式获得openid
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
        $access_token_json = $this->https_request($access_token_url);
        $access_token_array = json_decode($access_token_json, true);
        $openid = $access_token_array['openid'];
  
        //全局access_token获得用户基本信息
        $userinfo_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid";
        $userinfo_json = $this->https_request($userinfo_url);
        $userinfo_array = json_decode($userinfo_json, true);
        return $userinfo_array;
    }

    protected function https_request($url,$data=null){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            if (!empty($data)){
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);
            return $output;
    }





    /*主页*/
    public function home(){
        $openid = session('openid');
        //两条新闻 标题 日期 封面
        $newWhere['bl_state'] = 1; 
        $newRes = D("news")->where($newWhere)->field('int_id,vc_title,vc_cover,dt_date')->limit(2)->order('dt_date desc')->select();
        $this->assign('newRes',$newRes);
        
        //培训专栏
        $peixunWhere['bl_state'] = 1; 
        $peixunRes = D("peixun")->where($peixunWhere)->field('int_id,vc_name,vc_photo,vc_tag')->select();
        $this->assign('peixunRes',$peixunRes);
        session('openid',$openid);
        $this->assign('openid',$openid);
        $this->assign('tittle','电力培训');
        $this->display();
    }

    public function peixunInfo(){
        $openid = session('openid');
        $id = $_GET['id'];
        $peixunWhere['bl_state'] = 1; 
        $peixunWhere['int_id'] = $id; 
        $content = D("peixun")->where($peixunWhere)->getField("text_content");
        $this->assign('content',$content);
        $this->assign('tittle','培训专栏');
        $this->display();
    }

    public function guanggao(){
        $content = D("base")->where("vc_name='guanggao'")->getField("vc_value");
        $this->assign('tittle','广告');
        $this->assign('content',$content);
        $this->display('shouduan');
    }

    /*就业指导页面*/
    public function jiuyezhidao(){
        $openid = session('openid');
        $tel = D("base")->where("vc_name='jiuyezhidaoTel'")->getField("vc_value");

        $coverStr = D("base")->where("vc_name='jiuyeCover'")->getField("vc_value");
        if($coverStr==''||$coverStr==NULL){
            $cover = '';
        }else{
            $cover = explode('&&', trim($coverStr,'&&'));
        }
        
        $word = D("base")->where("vc_name='jiuyeWord'")->getField("vc_value");
        $movie = D("base")->where("vc_name='jiuyeMovie'")->getField("vc_value");
        $this->assign('tel',$tel);
        $this->assign('cover',$cover);
        $this->assign('word',$word);
        $this->assign('movie',$movie);
        $this->assign('openid',$openid);
        $this->assign('tittle','电力培训');
        $this->display();
    }

    public function jiuyePage(){
        $num = $_GET['num'];
        $page = 'jiuyePage'.$num;
        $content = D("base")->where("vc_name='$page'")->getField("vc_value");
        $this->assign('tittle','就业指导');
        $this->assign('content',$content);
        $this->display('shouduan');
    }

    /*岗位列表页，判断是否有简历*/
    public function gangwei(){
        $openid = session('openid');
        $where['vc_openid'] = $openid;
        $where['bl_state'] = 1;
        $res = D("Jianli")->where($where)->find();
        //如果没有简历，进入简历编辑页
        if($res == NULL){
            $this->redirect('Index/jianli');
        }
        //如果有简历，进入岗位列表页
        else {
            $gangweiList = D("Work")->where("bl_state=1")->Field('int_id,vc_name,vc_job,vc_img,vc_educational,vc_major')->select();
            $this->assign('gangweiList',$gangweiList);
            $this->assign('baseurl',URL);
            $this->assign('show',$_GET['show']);
            $this->assign('tittle','就业指导');
            $this->display();
        }
        
    }

    /*岗位详情页*/
    public function gangweiInfo(){
        $openid = session('openid');
        $id = $_GET['id'];
        $where['int_id'] = $id;
        $where['bl_state'] = 1;
        $gangweiInfo = D("Work")->where("bl_state=1")->find();
        $describe = $gangweiInfo['text_describe'];
        $company = $gangweiInfo['text_company'];
        $gangweiInfo['text_describe'] = str_replace("&&", "<br/>", $describe);
        $gangweiInfo['text_company'] = str_replace("&&", "<br/>", $company);
        $this->assign('gangweiInfo',$gangweiInfo);
        $this->assign('baseurl',URL);
        $this->assign('tittle','就业指导');
        $this->display();
    }

    /*提交简历页面*/
    public function jianli(){
        $openid = session('openid');
        //查询该openid的简历
        $where['vc_openid'] = $openid;
        $where['bl_state'] = 1;
        $jianliInfo = D("Jianli")->where($where)->find();
        if($jianliInfo !== NULL){
            $this->assign('jianliInfo',$jianliInfo); 
        }
        $this->assign('tittle','电力培训');
        $this->display();
    }
    /*提交简历操作*/
    public function jianliAction(){
        $openid = session('openid');
        $mop['vc_openid'] = $openid;
        $mop['vc_name'] = $_POST['name'];
        $mop['vc_sex'] = $_POST['sex'];
        $mop['vc_age'] = $_POST['age'];
        $mop['vc_educational'] = $_POST['educational'];
        $mop['vc_graduation_school'] = $_POST['graduation'];
        $mop['vc_major'] = $_POST['major'];
        $mop['vc_ideal_job'] = $_POST['job'];
        $mop['vc_telephone'] = $_POST['telephone'];
        $mop['vc_exprience'] = $_POST['exprience'];
        $mop['bl_state'] = 1;
        $where['vc_openid'] = $openid;
        $where['bl_state'] = 1;
        $jianliInfo = D("Jianli")->where($where)->find();
        if($jianliInfo==NULL){
            $res = D("Jianli")->add($mop);
        }else{
            $res = D("Jianli")->where($where)->save($mop);
        }
        
        $this->redirect("Index/gangwei");
        
    }
    
    /*申请自荐*/
    public function zijian(){
        $openid = session('openid');
        $id = $_GET['id'];
        //自荐表中查询该openid及岗位id
        $where['vc_openid'] = $openid;
        $where['int_work'] = $id;
        $zijian = D("Zijian")->where($where)->find();
        $this->assign('id',$id);
        if($zijian == NULL){
            $mop['vc_openid'] = $openid;
            $mop['int_work'] = $id;
            $mop['dt_date'] = date('Y-m-d',time());
            $res = D('Zijian')->add($mop);
            if($res){
                $this->assign('show','申请成功!');
                $this->display();
            }
        }else{
            $this->assign('show','您已对该岗位申请过自荐，请换一个吧！');
            $this->display();
        }
    }




    /*现场疑难（人才成长）*/
        //专家列表及已有问题
    public function xianchangyinan(){
        $openid = session('openid');
        //专家列表
        $zhuanjiaWhere['bl_state'] = 1; 
        $zhuanjiaRes = D("zhuanjia")->where($zhuanjiaWhere)->field('int_id,vc_name,vc_photo,vc_job')->select();
        $this->assign('zhuanjiaRes',$zhuanjiaRes);
        //已有问题
        $wentiWhere['bl_state'] = 1; 
        $wentiWhere['vc_type'] = "1";
        $wentiRes = D("Question")->where($wentiWhere)->field('int_id,vc_tag,text_question')->select();

        $tishiWhere['bl_state'] = 1; 
        $tishiRes = D("zhuanjiatishi")->where($tishiWhere)->select();
        $tel = D("base")->where("vc_name='xianchangyinanTel'")->getField("vc_value");
        $this->assign('tel',$tel);
        $this->assign('wentiRes',$wentiRes);
        $this->assign('tishiRes',$tishiRes);
        $this->assign('openid',$openid);
        $this->assign('tittle','现场疑难');
        $this->display();
    }

    public function tishiInfo(){
        $openid = session('openid');
        $id = $_GET['id'];
        $tishiWhere['bl_state'] = 1; 
        $tishiWhere['int_id'] = $id; 
        $content = D("zhuanjiatishi")->where($tishiWhere)->getField("text_tishi");
        $this->assign('content',$content);
        $this->assign('tittle','专家提示');
        $this->display();
    }

    public function yinanPage(){
        $num = $_GET['num'];
        $page = 'yinanPage'.$num;
        $content = D("base")->where("vc_name='$page'")->getField("vc_value");
        $this->assign('tittle','现场疑难');
        $this->assign('content',$content);
        $this->display('shouduan');
    }

    /*现场疑难线上留言*/
        //选择类型
    public function yinanLiuyan(){
        $this->assign('tittle','现场疑难');
        $this->display();
    }

    /*提交留言*/
    public function yinanLiuyanAction(){
        $openid = session('openid');
        $vip = session('vip');
        /*if($vip==0){

        }else if($vip==1){
            $gold = D("user")->where("vc_openid='$openid'")->getField('int_fanxian');
            if($gold<50){
                $this->display('yuenot');
            }else if($gold>=50){
                $mop['vc_openid'] = $openid;
                $mop['vc_type'] = '1';
                $mop['vc_tag'] = $_POST['type'];
                $mop['text_question'] = $_POST['word'];
                $mop['dt_date'] = date('Y-m-d',time());
                $mop['bl_state'] = 1;
                
                $res = D("Question")->add($mop);
                
                if($res){
                    $this->assign('show','留言成功');
                    $this->display();
                }
            }
        }*/
        $mop['vc_openid'] = $openid;
                $mop['vc_type'] = '1';
                $mop['vc_tag'] = $_POST['type'];
                $mop['text_question'] = $_POST['word'];
                $mop['dt_date'] = date('Y-m-d',time());
                $mop['bl_state'] = 1;
                
                $res = D("Question")->add($mop);
                
                if($res){
                    $this->assign('show','留言成功');
                    $this->display();
                }
    }
    






    /*岗位提升（岗位提升）*/
        //专家列表及已有问题
    public function gangweitisheng(){
        $openid = session('openid');
        //专家列表
        $zhuanjiaWhere['bl_state'] = 1; 
        $zhuanjiaRes = D("zhuanjia")->where($zhuanjiaWhere)->field('int_id,vc_name,vc_photo,vc_job')->select();
        $this->assign('zhuanjiaRes',$zhuanjiaRes);
        //已有问题
        $wentiWhere['bl_state'] = 1; 
        $wentiWhere['vc_type'] = "2";
        $wentiRes = D("Question")->where($wentiWhere)->field('int_id,vc_tag,text_question')->select();
        $tel = D("base")->where("vc_name='gangweitishengTel'")->getField("vc_value");
        $this->assign('tel',$tel);
        $this->assign('wentiRes',$wentiRes);
        $this->assign('openid',$openid);
        $this->assign('tittle','岗位提升');
        $this->display();
    }

    public function shouduan(){
        $num = $_GET['num'];
        $page = 'tishengshouduan'.$num;
        $content = D("base")->where("vc_name='$page'")->getField("vc_value");
        $this->assign('tittle','提升手段');
        $this->assign('content',$content);
        $this->display();
    }

    /*岗位提升线上留言*/
        //选择类型
    public function tishengLiuyan(){
        $this->assign('tittle','岗位提升');
        $this->display();
    }
    /*提交留言*/
    public function tishengLiuyanAction(){
        $openid = session('openid');
        $mop['vc_openid'] = $openid;
        $mop['vc_type'] = '2';
        $mop['vc_tag'] = $_POST['type'];
        $mop['text_question'] = $_POST['word'];
        $mop['dt_date'] = date('Y-m-d',time());
        $mop['bl_state'] = 1;
        
        $res = D("Question")->add($mop);
        
        if($res){
            $this->assign('show','留言成功');
            $this->display();
        }
    }

    /*问题详情*/
    public function questionInfo(){
        $openid = session('openid');
        $id = $_GET['id'];
        $yinanWhere['bl_state'] = 1; 
        $yinanWhere['int_id'] = $id; 
        $yinanRes = D("Question")->where($yinanWhere)->find();
        $this->assign('yinanRes',$yinanRes);

        $answerWhere['int_question_id'] = $id;
        $answerRes = D("Answer")->where($answerWhere)->select();
        $this->assign('answerRes',$answerRes);

        $this->assign('tittle','问题详情');
        $this->display();
    }


    /*公共基础（资料）*/
    public function gonggongjichu(){
        //展示开放免费资料
        $type = '1';
        $where['bl_type'] = $type;
        $limit = NULL;
        $ziliaoList = D("ziliao")->where($where)->order('dt_date desc')->limit($limit)->select();
        $list = $this->ziliaoList($ziliaoList);
        $this->assign("top",'null');
        $this->assign('list',$list);
        $this->assign('tittle','公共基础');
        $this->display('ziliao');
    }
    /*专项部分（资料）*/
    public function zhuanxiangbufen(){
        //展示vip收费资料
        $type = '2';
        $where['bl_type'] = $type;
        $limit = 10;
        $ziliaoList = D("ziliao")->where($where)->order('dt_date desc')->limit($limit)->select();
        $list = $this->ziliaoList($ziliaoList);

        //一级分类
        $toplist = D("ziliaobiaoqian")->where("int_parentid=0 and bl_state=1")->select();
        foreach ($toplist as $k => $v) {
            $top[$k]['biaoqianid'] = $v['int_id'];
            $top[$k]['biaoqianname'] = $v['vc_name'];
        }
        $this->assign("top",$top);
        $this->assign('list',$list);
        $this->assign('tittle','专项部分');
        $this->display('ziliao');
    }

    public function ziliaoIndex(){
        $index = $_GET['index'];
        $biaoqianlist = D("ziliaobiaoqian")->where("int_parentid='$index' and bl_state=1")->select();
        foreach ($biaoqianlist as $k => $v) {
            $top[$k]['biaoqianid'] = $v['int_id'];
            $top[$k]['biaoqianname'] = $v['vc_name'];
        }
        $where['vc_biaoqian'] = array('like',array($index.'&&%','&&'.$index.'&&%',$index,'%&&'.$index),'OR');
        $limit = 10;
        $ziliaoList = D("ziliao")->where($where)->order('dt_date desc')->limit($limit)->select();
        $list = $this->ziliaoList($ziliaoList);
        $this->assign('list',$list);
        $this->assign("top",$top);
        $this->assign('tittle','专项部分检索');
        $this->display('ziliao');
    }

    protected function ziliaoList($ziliaoList){
        /*$where['bl_type'] = $type;
        $ziliaoList = D("ziliao")->where($where)->order('dt_date desc')->limit($limit)->select();*/
        foreach ($ziliaoList as $key => $value) {
            $list[$key]['id'] = $value['int_id'];
            $coverStr = $value['vc_cover'];
            $coverArr = explode('&&', $coverStr);
            if($coverStr=='' || $coverStr==NULL){
                $list[$key]['cover'] = "Public/Home/image/home-ziliao-two.png";//默认图
            }else{
                $list[$key]['cover'] = $coverArr[0];//图
            }
            
            $list[$key]['title'] = $value['vc_title'];//标题
            $biaoqianStr = $value['vc_biaoqian'];
            $biaoqianArr = explode('&&', trim($biaoqianStr,'&&'));
            foreach ($biaoqianArr as $k => $v) {
                $biaoqianName = D("ziliaobiaoqian")->where("int_id='$v'")->getField('vc_name');
                $list[$key]['biaoqian'] .= $biaoqianName.' ';//标签
            }
        }
        return $list;
    }

    

    public function ziliaoContent(){
        $index = $_GET['index'];
        $where['int_id'] = $index;
        $ziliaoContent = D("ziliao")->where($where)->find();
        $type = $ziliaoContent['bl_type'];
        //标签
        $biaoqianStr = $ziliaoContent['vc_biaoqian'];
        $biaoqianArr = explode('&&', trim($biaoqianStr,'&&'));
        foreach ($biaoqianArr as $key => $value) {
            $biaoqianName = M("Ziliaobiaoqian",'dl_')->where("int_id='$value'")->getField('vc_name');
            $biaoqian[$key] = $biaoqianName;
        }
        //图片
        $coverStr = $ziliaoContent['vc_cover'];
        $coverArr = explode('&&', trim($coverStr,'&&'));

        foreach ($coverArr as $k => $v) {
            if($coverStr=='' || $coverStr==NULL){
                $cover = '';
            }else{
                $cover[$key] = $v;//图
            }
        }
        //是否vip
        $vip = session('vip');
        $this->assign("vip",$vip);
        $this->assign("type",$type);
        $this->assign("cover",$cover);
        $this->assign("biaoqian",$biaoqian);
        $this->assign('content',$ziliaoContent);
        $this->assign('tittle','资料详情');
        $this->display();
    }


    /*新闻列表页*/
    public function newsList(){
        //新闻 标题 日期 封面
        $newWhere['bl_state'] = 1; 
        $newRes = D("news")->where($newWhere)->field('int_id,vc_title,vc_cover,dt_date')->select();
        $this->assign('newRes',$newRes);
        $this->assign('tittle','新闻');
        $this->display();
    }
    /*新闻详情页*/
    public function newsInfo(){
        $newsID = $_GET['id'];
        $newWhere['int_id'] = $newsID; 
        $newRes = D("news")->where($newWhere)->find();
        $this->assign('newRes',$newRes);
        $this->assign('tittle','新闻');
        $this->display();
    }

    /*专家列表页*/
    /*public function zhuanjiaList(){
        //专家 姓名 头像 职务
        $zhuanjiaWhere['bl_state'] = 1; 
        $zhuanjiaRes = D("zhuanjia")->where($zhuanjiaWhere)->field('int_id,vc_name,vc_photo,vc_job')->select();
        $this->assign('zhuanjiaRes',$zhuanjiaRes);

        $this->assign('openid',$openid);
        $this->assign('tittle','电力培训');
        $this->display();
    }*/
    /*专家详情页*/
    public function zhuanjiaInfo(){
        $zhuanjiaID = $_GET['id'];
        $zhuanjiaWhere['int_id'] = $zhuanjiaID; 
        $zhuanjiaRes = D("zhuanjia")->where($zhuanjiaWhere)->find();
        //将&&替换成<br>
        $jieshao = $zhuanjiaRes['text_jieshao'];
        $techang = $zhuanjiaRes['text_techang'];
        $zhuanjiaRes['text_jieshao'] = str_replace("&&", "<br/>", $jieshao);
        $zhuanjiaRes['text_techang'] = str_replace("&&", "<br/>", $techang);
        $this->assign('zhuanjiaRes',$zhuanjiaRes);
        $this->assign('tittle','专家');
        $this->display();
    }

    //openid不一致的错误页面
    public function error(){
        $this->assign('tittle','错误页面');
        $this->display();
    }








































    
	//首页新闻列表4个
    public function newst(){
        $where['bl_state'] = 1;
        $list = D("news")->where($where)->order('dt_date desc')->select();
        foreach ($list as $key => $value) {
            $info[$key]['id'] = $value['int_id'];
            $info[$key]['title'] = $value['vc_title'];
            $info[$key]['img'] = $value['vc_img'];
        }
        if($list==false||$list==NULL){
            $output = array(
            'data'=>'',
            'info'=>'暂时没有新闻动态！',
            'code'=>'-200',
            );
        }else{
            $output = array(
            'data'=>$info,
            'info'=>'success',
            'code'=>'100',
            );
        }
        
        exit(json_encode($output));
    }

    //打开新闻
    public function newsContent(){
        $id = $_POST['newsID'];
        $where['int_id'] = $id;
        $content = D("news")->where($where)->find();
        $info['title'] = $content['vc_title'];
        $info['cover'] = $content['vc_img'];
        $info['content'] = $content['text_content'];
        if($info==NULL || $info==false){
            $output = array(
            'data'=>NULL,
            'info'=>'内容为空，请上传后重试!',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>$info,
            'info'=>'success',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }

    //商品列表
    public function productList(){
        $where['bl_state'] = 1;
        $list = D("product")->where($where)->select();
        foreach ($list as $key => $value) {
            $info[$key]['productID'] = $value['int_id'];
            $info[$key]['productName'] = $value['vc_name'];
            $info[$key]['productImg'] = $value['vc_picture'];
            $info[$key]['productPrice'] = $value['int_price']/100;
            $info[$key]['productCount'] = $value['int_count'];
        }
        if($list==NULL || $list==false){
            $output = array(
            'data'=>NULL,
            'info'=>'暂时没有商品',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>$info,
            'info'=>'success',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }

    //商品详情
    public function productContent(){
        $productID = $_POST['pid'];
        $where['int_id'] = $productID;
        $where['bl_state'] = 1;
        $list = D("product")->where($where)->find();
        
        $info['productID'] = $list['int_id'];
        $info['productName'] = $list['vc_name'];
        $info['productImg'] = $list['vc_picture'];
        $info['productPrice'] = $list['int_price']/100;
        $info['productCount'] = $list['int_count'];
        if($list==NULL || $list==false){
            $output = array(
            'data'=>NULL,
            'info'=>'暂时没有商品',
            'code'=>-201,
            );
        }else{
            $output = array(
            'data'=>$info,
            'info'=>'success',
            'code'=>100,
            );
        }
        exit(json_encode($output));
    }

    //公众号菜单我的二维码
    public function erweima(){
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $code = $_GET["code"];
        $access_token = $this->gettoken($appid,$appsecret);
        $userinfo = $this->getUserInfo($appid,$appsecret,$access_token,$code);
        //信息存入数据库
        $openid=$userinfo['openid'];
        $check = D("user")->where("weixinid='$openid'")->find();
        if($check==NULL){
            $time = $userinfo['subscribe_time'];
            $info['weixinname'] = $userinfo['nickname'];
            $info['weixinimage'] = $userinfo['headimgurl'];
            $info['weixinid'] = $openid;
            $info['time'] = date("Y-m-d H-i-s",$time);
            if($openid){
                $res = D("user")->add($info);
            }else{
                $url = URL."Public/html/error.html";
                redirect($url);
                exit();
            }
        }else if($check['weixinname']==NULL || $check['weixinname']==''){
            $time = $userinfo['subscribe_time'];
            $info['weixinname'] = $userinfo['nickname'];
            $info['weixinimage'] = $userinfo['headimgurl'];
            $info['time'] = date("Y-m-d H-i-s",$time);
            $res = D("user")->where("weixinid='$openid'")->save($info);
        }       
        $url = "http://wangxiaohuawsd.cn/hkmy/Public/html/extension-erweima-ewm.html?openid=".$openid;
        redirect($url);
    }

    //公众号菜单推广
    public function tuiguang(){
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $code = $_GET["code"];
        $access_token = $this->gettoken($appid,$appsecret);
        $userinfo = $this->getUserInfo($appid,$appsecret,$access_token,$code);
        //信息存入数据库
        $openid=$userinfo['openid'];
        $check = D("user")->where("weixinid='$openid'")->find();
        if($check==NULL){
            $time = $userinfo['subscribe_time'];
            $info['weixinname'] = $userinfo['nickname'];
            $info['weixinimage'] = $userinfo['headimgurl'];
            $info['weixinid'] = $openid;
            $info['time'] = date("Y-m-d H-i-s",$time);
            if($openid){
                $res = D("user")->add($info);
            }else{
                $url = URL."Public/html/error.html";
                redirect($url);
                exit();
            }
        }else if($check['weixinname']==NULL || $check['weixinname']==''){
            $time = $userinfo['subscribe_time'];
            $info['weixinname'] = $userinfo['nickname'];
            $info['weixinimage'] = $userinfo['headimgurl'];
            $info['time'] = date("Y-m-d H-i-s",$time);
            $res = D("user")->where("weixinid='$openid'")->save($info);
        }       
        $url = "http://wangxiaohuawsd.cn/hkmy/Public/html/extension.html?openid=".$openid;
        redirect($url);
    }

    //公众号菜单分销
    public function fenxiao(){
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $code = $_GET["code"];
        $access_token = $this->gettoken($appid,$appsecret);
        $userinfo = $this->getUserInfo($appid,$appsecret,$access_token,$code);
        //信息存入数据库
        $openid=$userinfo['openid'];
        $check = D("user")->where("weixinid='$openid'")->find();
        if($check==NULL){
            $time = $userinfo['subscribe_time'];
            $info['weixinname'] = $userinfo['nickname'];
            $info['weixinimage'] = $userinfo['headimgurl'];
            $info['weixinid'] = $openid;
            $info['time'] = date("Y-m-d H-i-s",$time);
            if($openid){
                $res = D("user")->add($info);
            }else{
                $url = URL."Public/html/error.html";
                redirect($url);
                exit();
            }
        }else if($check['weixinname']==NULL || $check['weixinname']==''){
            $time = $userinfo['subscribe_time'];
            $info['weixinname'] = $userinfo['nickname'];
            $info['weixinimage'] = $userinfo['headimgurl'];
            $info['time'] = date("Y-m-d H-i-s",$time);
            $res = D("user")->where("weixinid='$openid'")->save($info);
        }       
        $url = "http://wangxiaohuawsd.cn/hkmy/Public/html/commodity-jrfx.html?openid=".$openid;
        redirect($url);
    }

}