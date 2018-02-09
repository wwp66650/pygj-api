<?php
/**
 * Created by PhpStorm.
 * User: una
 * Date: 2018/2/7
 * Time: 16:03
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    /**
     * 申请邀请码
     * @param Request $request
     * @return static
     */
    public function appInviteCode(Request $request){
        $userId = $request->user()->id;
        $types = $request->input('types');
        $num = $request->input('codenum');

        if(!preg_match('/^[1-9]\d?\d?$/', $num)){
            return $this->ajaxError('数量应该为1-999');
        }

        if(!preg_match('/^-1|30|90|365|5|1$/', $types)){
            return $this->ajaxError("请输入正确的邀请码类型");
        }

        try{
            (new OrderService())->appInviteCode($userId, $types, $num);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }

    /**
     * 续费
     * @param Request $request
     * @return static
     */
    public function renewFee(Request $request){
        $userId = $request->user()->id;
        $phone = $request->input('phone');
        $types = $request->input('types');

        // 判断参数.
        if(!preg_match('/^1[3456789]{1}\d{9}$/', $phone)){
            return $this->ajaxError('请输入正确的手机号码');
        }
        if(!preg_match('/^30|90|365$/', $types)){
            return $this->ajaxError('请输入正确的续费类型');
        }

        try{
            (new OrderService())->renewFee($userId, $phone, $types);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();

    }

    /**
     * 升级终身码
     * @param Request $request
     * @return static
     */
    public function upVip(Request $request){
        $userId = $request->user()->id;
        $newCode = $request->input('new_code');

        if(!preg_match('/^\w{1,6}$/', $newCode)){
            return $this->ajaxError('参数错误');
        }

        try{
            (new OrderService())->upVip($userId, $newCode);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }

    /**
     * 提现申请
     * @param Request $request
     * @return static
     */
    public function withdrawal(Request $request){
        $userId = $request->user()->id;
        $money = $request->input('money');

        // 提交申请.
        try{
            (new OrderService())->withdrawal($userId, $money);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }

    /**
     * 可提现金额
     * @param Request $request
     * @return static
     */
    public function withdrawalsNum(Request $request){
        $userId = $request->user()->id;

        try{
            $data = (new OrderService())->withdrawalsNum($userId);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess($data);
    }

    /**
     * 转让邀请码
     * @param Request $request
     * @return static
     */
    public function transfer(Request $request){
        $userId = $request->user()->id;
        $code = $request->input('code');
        $types = $request->input('types');
        $toName = $request->input('to_name');
        $toPhone = $request->input('to_phone');

        if(!$code){
            return $this->ajaxError('参数错误');
        }

        if(!preg_match('/^-1|30|90|365|5$/', $types)){
            return $this->ajaxError("邀请码类型不正确");
        }

        if(!preg_match('/^[\x{4e00}-\x{9fa5}]{2,20}$|^[a-zA-Z\s]*[a-zA-Z\s]{2,30}$/isu', $toName)){
            return $this->ajaxError('姓名只支持中文或英文');
        }

        if(!preg_match('/^1[3456789]{1}\d{9}$/', $toPhone)){
            return $this->ajaxError('请输入正确的手机号码');
        }

        try{
            (new OrderService())->transfer($userId, $code, $types, $toName, $toPhone);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }

    /**
     * 邀请订单
     * @param Request $request
     * @return static
     * @throws \Exception
     */
    public function acceptInvite(Request $request){
        $masterPhone = $request->input('master_phone');
        $phone = $request->input('phone');
        $types = $request->input('types');

        if(!preg_match('/^1[3456789]{1}\d{9}$/', $phone)){
            return $this->ajaxError('手机号格式不正确');
        }

        if(!preg_match('/^-1|30|90|365|1$/', $types)){
            return $this->ajaxError("请输入正确的邀请码类型");
        }

        try{
            (new OrderService())->acceptInvite($masterPhone, $phone, $types);
        }catch (\Exception $e){
            return $this->ajaxError($e->getMessage());
        }

        return $this->ajaxSuccess();
    }

}