<?php
/**
 * 服务端氪金页面
 */
$title = '服务端充值';
include 'header.php';
?>
<div class="row" id="App">
    <div class="col-12" v-if="MyIslandData">
        <div class="card">
            <div class="card-header">
                服务端充值公告
            </div>
            <div class="card-body" v-html="MyIslandData.Pay.Tips.content">
            </div>
        </div>
    </div>
    <div class="col-12" v-if="MyIslandData">
        <div class="card">
            <div class="card-header">
                我的商城信息 - <a style="color: red;font-size: 1.2em" href="admin.goods.supply.php">点击前往供货大厅</a>
            </div>
            <div class="card-body">
                <fieldset class="layui-elem-field layui-anim layui-anim-up">
                    <legend>商城海热度排名：第{{MyIslandData.rank}}名
                        <i class="mdui-icon material-icons" @click="MyIslandGet(2)"
                           style="font-size:0.9em;cursor:pointer;">cached</i>
                    </legend>
                    <div class="layui-field-box">
                        <div class="mdui-table-fluid" style="border: none">
                            <table class="layui-table" style="white-space: nowrap;">
                                <thead>
                                <tr>
                                    <th>名称</th>
                                    <th>值</th>
                                    <th>说明</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr mdui-tooltip="{content: '热度指数可通过被他人点赞，或购买获得！，他人每次点赞可获得1-5点热度值！，每日签到可获得1-10点热度值！', position: 'top'}">
                                    <td>
                                        热度指数
                                    </td>
                                    <td>
                                        <span class="badge badge-info-lighten">{{MyIslandData.data.Popular}}点</span>
                                        <span style="cursor:pointer;"
                                              @click="BuyingEnthusiasm()"
                                              class="badge badge-danger-lighten mdui-m-l-1">购买</span>
                                        <span v-if="MyIslandData.data.CheckTime==-1" style="cursor:pointer;"
                                              @click="SignDaily(MyIslandData.data.CheckTime)"
                                              class="badge badge-danger-lighten mdui-m-l-1">
                                        签到
                                    </span>
                                        <span v-else style="cursor:pointer;"
                                              @click="SignDaily(MyIslandData.data.CheckTime)"
                                              class="badge badge-warning-lighten mdui-m-l-1">
                                        已签
                                    </span>
                                    </td>
                                    <td>热度值会缓慢增长,每天约可增加1-10点左右</td>
                                </tr>
                                <tr>
                                    <td>站点等级</td>
                                    <td>
                                    <span class="badge badge-primary-lighten">
                                        Lv {{MyIslandData.data.grade[0]}}
                                    </span>
                                        <span class="badge">
                                        [ {{MyIslandData.data.grade[1]}} / {{MyIslandData.data.grade[2]}} ]
                                    </span>
                                    </td>
                                    <td>等级[成长值,升级所需成长值]，成长值越高,可信度越高，可通过他人点赞,每日签到获得！
                                    </td>
                                </tr>
                                <tr>
                                    <td>我的余额</td>
                                    <td style="color:rgba(240,58,58,0.76);font-size: 2em;">{{MyIslandData.money}}元</td>
                                    <td>服务端可用余额，可以用来购买应用商店内的商品，以及官方供货区内的商品！</td>
                                </tr>
                                <tr>
                                    <td>加入时间</td>
                                    <td>{{MyIslandData.data.addtime}}</td>
                                    <td>加入商城海的时间</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                服务端充值
            </div>
            <div class="card-body">
                <fieldset class="layui-elem-field">
                    <legend>充值金额</legend>
                    <div class="layui-field-box">
                        <label class="mdui-radio" style="margin-right:0.5rem">
                            <input @click="SelectMyMoney(10)" value="10" checked type="radio" name="moeny"/>
                            <i class="mdui-radio-icon"></i>充值10元
                        </label>
                        <label class="mdui-radio" style="margin-right:0.5rem">
                            <input @click="SelectMyMoney(50)" value="50" type="radio" name="moeny"/>
                            <i class="mdui-radio-icon"></i>充值50元
                        </label>
                        <label class="mdui-radio" style="margin-right:0.5rem">
                            <input type="radio" @click="SelectMyMoney(100)" value="100" name="moeny"/>
                            <i class="mdui-radio-icon"></i>充值100元
                        </label>
                        <label class="mdui-radio" style="margin-right:0.5rem">
                            <input type="radio" @click="SelectMyMoney(500)" value="500" name="moeny"/>
                            <i class="mdui-radio-icon"></i>充值500元
                        </label>
                        <label class="mdui-radio" style="margin-right:0.5rem">
                            <input type="radio" @click="SelectMyMoney(1000)" value="1000" name="moeny"/>
                            <i class="mdui-radio-icon"></i>充值1000元
                        </label>
                        <label class="mdui-radio">
                            <input type="radio" @click="SelectMyMoney(2000)" value="2000" name="moeny"/>
                            <i class="mdui-radio-icon"></i>充值2000元
                        </label>
                        <div class="layui-form-item">
                            <div class="layui-input-group">
                                <div class="layui-input-split layui-input-prefix">
                                    自定义金额
                                </div>
                                <input type="number" min="10" max="2000" v-model="MyMoney" placeholder="自定义充值金额"
                                       class="layui-input">
                                <div class="layui-input-suffix">
                                    元
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="layui-elem-field">
                    <legend>付款方式</legend>
                    <div class="layui-field-box" v-html="PayHtml"></div>
                </fieldset>
                <button class="mdui mdui-btn mdui-btn-block mdui-color-blue-grey" @click="PayBuy()">开始充值</button>
            </div>
        </div>
    </div>
</div>
<?php include 'bottom.php'; ?>
<script src="../assets/js/vue3.js"></script>
<script>
    const App = Vue.createApp({
        data() {
            return {
                MyMoney: 10, //充值金额
                MyPayType: '', //付款方式
                MyPayTypeZ: '', //付款参数
                PayHtml: '正在加载中...',
                MyIslandData: false, //我的商城数据
            }
        },
        methods: {
            SignDaily(t) { //每日签到
                if (t != -1) {
                    mdui.dialog({
                        title: '通知',
                        content: '您今日已经签到过了，签到时间：' + t + '<hr>若想要获得更多热度值，可通过购买，或其他站点点赞获得！',
                        modal: true,
                        history: false,
                        buttons: [{
                            text: '关闭',
                        }]
                    });
                    return false;
                }

                mdui.dialog({
                    title: '签到提醒',
                    content: '热度签到请前往供货大厅【我的商城】内签到哦！',
                    modal: true,
                    history: false,
                    buttons: [{
                        text: '关闭',
                    }, {
                        text: '前往供货大厅', onClick: function () {
                            window.location.href = 'admin.goods.supply.php';
                        }
                    },],
                    onOpen: function () {

                    }
                });
            }, BuyingEnthusiasm() {
                mdui.dialog({
                    title: '热度购买提醒',
                    content: '热度购买价格：<font color=red size=5>1元=10点</font><br>热度可通过其他用户点赞获得，热度越高，则在商城海的排名越靠前，获得更多的展示机会！<hr>Ps：热度会缓慢增加，每天约增加1-10点左右，仅对公开展示状态的站点有效',
                    modal: true,
                    history: false,
                    buttons: [{
                        text: '关闭',
                    }, {
                        text: '购买热度', onClick: function () {
                            layer.prompt({
                                formType: 3, value: 10, title: '请输入购买金额，10元=100点热度值',
                            }, function (value, index, elem) {
                                let count = parseInt(value - 0);
                                if (count < 1) {
                                    layer.msg('最低购买1元热度值(10点)', {icon: 2});
                                    return false;
                                }

                                if (App.MyIslandData.money < count) {
                                    layer.open({
                                        title: '警告',
                                        content: '当前服务端余额不足' + count + '元！<br>当前余额为：' + App.MyIslandData.money + '元<br>请先充值！',
                                        icon: 2,
                                        btn: ['充值', '更新数据', '取消'],
                                        btn1: function () {
                                            open('https://cdn.79tian.com/api/wxapi/view/index.php');
                                        },
                                        btn2: function () {
                                            App.MyIslandGet(2);
                                        }
                                    })
                                    return false;
                                }
                                layer.open({
                                    title: '温馨提示',
                                    icon: 3,
                                    content: '是否要购买<font color=red>' + count * 10 + '点</font>热度值?<br>消耗余额：<font color=red>' + count + '元</font><br>当前余额为：<font color=red>' + App.MyIslandData.money + '元</font>！',
                                    btn: ['确定购买', '取消'],
                                    btn1: function () {
                                        let is = layer.msg('购买中，请稍后...', {icon: 16, time: 9999999});
                                        $.ajax({
                                            type: "POST", url: 'main.php?act=MyIslandPurchase', data: {
                                                money: count
                                            }, dataType: "json", success: function (res) {
                                                layer.close(is);
                                                if (res.code == 1) {
                                                    layer.alert((!res.msg ? '异常' : res.msg), {
                                                        icon: 1, btn1: function () {
                                                            App.MyIslandGet(2);
                                                        }
                                                    });
                                                } else {
                                                    layer.alert((!res.msg ? '异常' : res.msg), {
                                                        icon: 2
                                                    });
                                                }
                                            }, error: function () {
                                                layer.msg('服务器异常！');
                                            }
                                        });
                                    }
                                })
                                layer.close(index);
                            });
                        }
                    }],
                    onOpen: function () {

                    }
                });
            },
            SelectMyMoney(money) {
                App.MyMoney = money;
            }, SelectMyPayType(type, ts) {
                App.MyPayType = type;
                App.MyPayTypeZ = ts;
            }, Pay() {
                let is = layer.msg('数据获取中，请稍后...', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST", url: './main.php?act=MyPayList', dataType: "json", success: function (res) {
                        layer.close(is);
                        let PayHtml = '';
                        if (res.code === 1) {
                            for (const payHtmlKey in res.data) {
                                let data = res.data[payHtmlKey];
                                for (const dataKey in data.data) {
                                    let paydata = data.data[dataKey];
                                    if (paydata.state == false) {
                                        continue;
                                    }
                                    PayHtml += `
<label class="mdui-radio" style="margin-right:0.5rem;">
    <input onclick="App.SelectMyPayType('` + data.key + `','` + dataKey + `')" ` + (App.MyPayType == data.key && App.MyPayTypeZ == dataKey ? 'checked="checked" ' : '') + ` type="radio" name="type"/>
    <i class="mdui-radio-icon"></i>
    [` + data.name + `] ` + paydata.name + `
</label>
                            `;
                                }
                            }

                            if (PayHtml === '') {
                                PayHtml = '当前无可用付款方式，请联系管理员充值！';
                            }
                        } else {
                            PayHtml = res.msg;
                        }
                        App.PayHtml = PayHtml;
                        App.MyIslandGet(1);
                    }
                });
            }, PayBuy() {
                if (App.MyPayType == '' || App.MyPayTypeZ == '') {
                    layer.msg('请选择付款方式！', {
                        icon: 2
                    });
                    return;
                }
                App.MyPayOrder();
            }
            , MyPayOrder() {
                let is = layer.msg('正在创建充值订单中，请稍后...', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST", url: 'main.php?act=MyPayOrder', data: {
                        money: App.MyMoney,
                    }, dataType: "json", success: function (res) {
                        layer.close(is);
                        if (res.code >= 0) {
                            App.MyPay(res.order);
                        } else {
                            layer.alert((!res.msg ? '异常' : res.msg), {
                                icon: 2
                            });
                        }
                    }, error: function () {
                        layer.msg('服务器异常！');
                    }
                });
            }, MyPay(order) {
                //开始付款
                let Data = App.MyIslandData.Pay;
                let is = layer.msg('正在生成付款界面,请稍后...', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST", url: 'main.php?act=MyPay', data: {
                        order: order,
                    }, dataType: "json", success: function (res) {
                        layer.close(is);
                        if (res.code >= 0) {
                            let is = layer.msg('数据获取中，请稍后...', {icon: 16, time: 9999999});
                            $.ajax({
                                type: "POST", url: 'main.php?act=MyPayTs', data: {
                                    order: order, key: App.MyPayTypeZ, type: App.MyPayType
                                }, dataType: "json", success: function (res3) {
                                    layer.close(is);
                                    if (res3.code == 1) {
                                        if (App.MyPayTypeZ === 'wxpayH5') {
                                            res3.type = 1;
                                            App.MyPayType = '<font color="red">非手机微信客户端</font>,如QQ,浏览器';
                                        }
                                        if (res3.type == 1) {
                                            //生成付款二维码
                                            let content = `
<fieldset class="layui-elem-field" style="text-align:center">
  <h4 style="margin-top:1rem">请使用【` + App.MyPayType + `】扫码付款` + res.data.payment + `元</h4>
  <div class="layui-field-box">
    <img src="` + Data.QrUrl + encodeURIComponent(res3.url) + `" style="width:258px;height:258px;box-shadow: 1px 1px 12px #eee" />
    <hr>
    订单号：` + res.data.order + `
    <hr>
    <a href="` + res3.url + `" target="_blank">点击打开付款地址</a>
  </div>
</fieldset>
                            `;
                                            mdui.dialog({
                                                title: res.data.name,
                                                content: content,
                                                history: false,
                                                buttons: [{
                                                    text: '取消付款',
                                                }, {
                                                    text: '我已经完成付款', onClick: function () {
                                                        App.MyIslandGet(3);
                                                    }
                                                }]
                                            });
                                        } else {
                                            layer.open({
                                                title: '温馨提示',
                                                content: '充值地址获取成功，点击下方确定按钮来跳转新页面充值！',
                                                icon: 1,
                                                btn: ['前往充值', '取消充值'],
                                                btn1: function () {
                                                    window.open(res3.url);
                                                    layer.open({
                                                        title: '温馨提示',
                                                        content: '付款后点击下方按钮刷新余额！',
                                                        icon: 16,
                                                        btn: ['我已完成付款', '取消'],
                                                        btn1: function () {
                                                            App.MyIslandGet(3);
                                                        }
                                                    })
                                                }
                                            })
                                        }
                                    } else {
                                        layer.alert(res3.msg, {
                                            icon: 2
                                        });
                                    }
                                }, error: function () {
                                    layer.msg('服务器异常！');
                                }
                            });
                        } else {
                            layer.alert((!res.msg ? '异常' : res.msg), {
                                icon: 2
                            });
                        }
                    }, error: function () {
                        layer.msg('服务器异常！');
                    }
                });
            },
            MyIslandGet(type = 1) {
                if (type === 1 && App.MyIslandData !== false) {
                    return;
                }
                let is = layer.msg('数据获取中，请稍后...', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST", url: 'main.php?act=MyIsland', dataType: "json", success: function (res) {
                        layer.close(is);
                        if (res.code == 1) {
                            if (type === 3 && App.MyIslandData.money != res.money) {
                                mdui.dialog({
                                    title: '充值成功',
                                    content: '您本次成功充值：' + (res.money - App.MyIslandData.money) + '元！',
                                    modal: true,
                                    history: false,
                                    buttons: [{
                                        text: '关闭',
                                    }]
                                });
                                App.MyIslandData = res;
                            } else {
                                App.MyIslandData = res;
                            }
                        } else {
                            layer.alert((!res.msg ? '异常' : res.msg), {
                                icon: 2
                            });
                        }
                    }, error: function () {
                        layer.msg('服务器异常！');
                    }
                });
            }
        }
    }).mount('#App');

    App.Pay();
</script>
