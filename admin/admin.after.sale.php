<?php
//售后管理
use Medoo\DB\SQL;

$title = '售后订单管理 - 仅显示发起售后工单的订单';
include 'header.php';
$DB = SQL::DB();
$addressList = $DB->select('address', [
    'name', 'phone', 'region', 'address', 'id'
], [
    'status' => 2,
]);
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                售后订单列表
                <a title="初始化" href="javascript:App.AfterList();" class="badge badge-warning"><i
                            class="layui-icon layui-icon-refresh-3"></i></a>
            </div>
            <div class="card-body" id="App">
                <div class="table-responsive" id="table">
                    <table class="table table-hover table-centered mb-0" style="font-size:0.9em;">
                        <thead>
                        <tr style="white-space: nowrap">
                            <th>操作</th>
                            <th>售后备注</th>
                            <th>售后发起原因</th>
                            <th>商品名称</th>
                            <th>售后进度</th>
                            <th>订单金额</th>
                            <th>订单成本</th>
                            <th>付款方式</th>
                            <th>购买者</th>
                            <th>下单信息</th>
                            <th>订单编号</th>
                            <th>售后发起时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item,index) in Data" :class="'Tab_'+item.tid"
                            :style="item.superior>=1?'display: none;':''">
                            <td>
                                <button :mdui-menu="'{target:\'#html_operation_'+item.tid+'\'}'"
                                        class="mdui-btn mdui-ripple mdui-color-white mdui-text-color-blue-grey mdui-shadow-0 mdui-btn-icon">
                                    <i class="mdui-icon material-icons">&#xe8b8;</i>
                                </button>
                                <ul class="mdui-menu mdui-menu-cascade"
                                    :id="'html_operation_'+item.tid">
                                    <li class="mdui-menu-item">
                                        <a @click="Gd(item.tid,item.title)" class="mdui-ripple">
                                            [点击查看]售后工单：{{ item.tid}}
                                        </a>
                                    </li>
                                    <li class="mdui-menu-item">
                                        <a href="javascript:" @click="finish(item.tid)"
                                           class="mdui-ripple">关闭售后,用户可重新发起售后</a>
                                    </li>
                                    <li class="mdui-menu-item">
                                        <a href="javascript:" @click="ReturnAddress(item.oid,item.tid,item.title)"
                                           class="mdui-ripple">配置订单退货地址,让用户退货</a>
                                    </li>
                                    <li class="mdui-menu-item">
                                        <a href="javascript:" @click="RemarkSet(item.oid,item.tid,item.remark)"
                                           class="mdui-ripple">设置订单备注[方便判断售后进度]</a>
                                    </li>
                                    <li class="mdui-menu-item">
                                        <a href="javascript:" @click="Refund(item.payment,item.price,item.oid)"
                                           class="mdui-ripple">直接退款,关闭售后,无法再次发起</a>
                                    </li>
                                </ul>
                            </td>
                            <td style="max-width: 8em; cursor: pointer;"
                                @click="RemarkSet(item.oid,item.tid,item.remark)">
                                {{item.remark}}
                            </td>
                            <td style="max-width: 8em; cursor: pointer;color:#6d73fa"
                                @click="Gd(item.tid,item.title)">
                                {{item.title}}
                            </td>
                            <td title="查看此商品的相关订单" style="max-width: 8em; cursor: pointer;">
                                <a :href="'admin.order.list.php?gid='+item.gid" target="_blank">{{ item.name
                                    }}</a>
                            </td>
                            <td>
                                <span v-if="item.type==1" style="color: red">待你处理</span>
                                <span v-if="item.type==2" style="color: #2732fd">待用户处理</span>
                                <span v-if="item.type==3" style="color: seagreen">售后已完成</span>
                                <span v-if="item.type==4" style="color: #ccc">售后已关闭</span>
                                <span v-if="item.type==5" style="color: #0AAB89">用户已评价</span>
                            </td>
                            <td>
                                {{item.price - 0}}元
                            </td>
                            <td>
                                {{item.money - 0}}元
                            </td>
                            <td>
                                {{item.payment}}
                            </td>
                            <td>
                                <a v-if="item.uid>=1" target="_blank" :href="'./admin.user.log.php?uid='+item.uid"
                                   class="badge badge-primary-lighten">{{item.uid}}</a>
                                <span v-else class="badge badge-dark-lighten">游客</span>
                            </td>
                            <td style="max-width: 8em;">
                                {{item.input}}
                            </td>
                            <td>
                                <a target="_blank" :href="'admin.order.list.php?name='+item.order">{{item.order}}<a>({{item.oid}})
                            </td>
                            <td>
                                {{item.addtime}}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="Data.length===0" class="text-center w-100 mt-3 font-w300">
                    {{ type==-1?'正在载入中,请稍后...':'一个需要处理的售后订单也没有' }}
                </div>
            </div>
            <div class="layui-card-body" style="text-align:center;">
                <div id="Page"></div>
            </div>
        </div>
    </div>
</div>
<style>
    #distpicker select {
        width: 100%;
        height: 38px;
        border: 1px solid #d2d6de;
        border-radius: 4px;
        padding: 5px;
        margin-bottom: 10px;
    }
</style>
<?php include 'bottom.php'; ?>
<script src="../assets/js/jquery.nestable.js"></script>
<script src="../assets/js/vue3.js"></script>
<script>
    const addressList = '<?=json_encode($addressList)?>';
    console.log(addressList);
    const App = Vue.createApp({
        data() {
            return {
                Data: [], page: 1, limit: 12, count: -1
            }
        }, methods: {
            //订单退货地址发送
            ReturnAddress(oid, tid, title) {
                try {
                    var addressListArr = JSON.parse(addressList);
                } catch (e) {
                    layer.alert('退货地址配置错误,请检查并删除异常退货地址！', {icon: 2, title: '警告'});
                    return;
                }
                if (addressListArr.length === 0) {
                    layer.alert('请先配置退货地址', {
                        icon: 2,
                        title: '温馨提示',
                        btn: ['前往配置', '取消'],
                        btn1: function () {
                            window.location.href = 'admin.return.address.php';
                        }
                    })
                    return;
                }
                let opens = '';
                for (let i = 0; i < addressListArr.length; i++) {
                    opens += `<option value="` + i + `">[` + addressListArr[i].id + `]` + addressListArr[i].region + ',' + addressListArr[i].address + `</option>`;
                }
                let content = `
<div id="distpicker" style="width:300px;">
                <select id="site_1" style="width: 100%;">` + opens + `</select>
</div>
                `;
                //开始选择退货地址
                layer.open({
                    title: '选择退货地址',
                    content: content,
                    btn: ['确定', '取消'],
                    btn1: function () {
                        layer.closeAll();
                        let index = $('#site_1').val();
                        let address = addressListArr[index].region + ',' + addressListArr[index].address;
                        let name = addressListArr[index].name;
                        let phone = addressListArr[index].phone;

                        let content = `恭喜！您的退货审核已通过face[太开心]
请将物品退回到下面地址，我方收到货并检查完货物情况后会为您进行退款处理！

退货地址信息如下：
姓名：` + name + `
电话：` + phone + `
地址：` + address + `

需要退还的货物发出后，请将退货物流单号，按照如下方式提交！
1、打开当前页面【售后工单内或订单列表售后按钮处打开】
2、输入： 退货物流单号：xxxxxxxxxxxxxxxxxx
3、点击：【提交回复】按钮来提交退货物流单号即可！

注意事项：其中的【xxxxxxxxxxxxxxxxxx】请替换成您退货的物流单号哦，否则可能将无法正常退款！
另外，请于72小时内，将物流单号提交完毕，否则会判定为退货失败！

如果有任何不理解的地方，可以在当前页面继续留言哦，客服会为您解惑~face[嘻嘻] `;

                        //开始配置快捷工单回复
                        layer.prompt({
                            formType: 2,
                            value: content,
                            title: '售后工单快捷回复',
                            area: ['350px', '350px']
                        }, function (value, index) {
                            layer.close(index);
                            $.ajax({
                                type: "post",
                                url: "ajax.php?act=Tickets&type=Supplementary",
                                data: {
                                    id: tid,
                                    content: value
                                },
                                dataType: "json",
                                success: function (data) {
                                    if (data.code == 1) {
                                        layer.alert('退货地址快捷回复成功！', {
                                            icon: 1,
                                            title: '温馨提示',
                                            end: function () {
                                                App.RemarkSets(tid, oid, '退货地址已发送');
                                                App.Gd(tid, title);
                                            }
                                        });
                                    } else {
                                        layer.msg(data.msg, {
                                            icon: 2
                                        });
                                    }
                                },
                                error: function () {
                                    layer.alert('加载失败！');
                                }
                            });
                        });

                    }
                });
            },
            Refund(payment, money, oid) {
                let ntitle = (payment === '积分兑换' ? '积分' : '金额')
                layer.open({
                    title: '请配置退款' + ntitle,
                    content: '订单购买' + ntitle + '：' + (money - 0) + (ntitle === '积分' ? '积分' : '元') + '<br>订单退款' + ntitle + '：<span id="Refund">' + (money - 0) + '</span>' + (ntitle === '积分' ? '积分' : '元') + '<br>请滑动配置需要退款的' + ntitle + '！<hr><div id="slides"></div><div style="margin-top: 2em;"><input type="number"  placeholder="请输入退款金额" id="RefundAmount" class="layui-input"></div>',
                    btn: ['退单', '取消'],
                    btn1: function () {
                        layer.closeAll();
                        if ($("#Refund").text() === "") {
                            alert("请将退款金额提交完整！")
                            return
                        }
                        let price = $("#Refund").text() - 0; //退款金额
                        $.ajax({
                            type: 'POST',
                            url: 'ajax.php?act=ChangeOrders',
                            data: {
                                id: 5,
                                did: oid, //订单ID
                                money: price,
                            },
                            dataType: 'json',
                            async: true,
                            success: function (data) {
                                if (data.code >= 1) {
                                    layer.msg('退款成功', {icon: 1, time: 1000}, function () {
                                        App.initialization();
                                    });
                                } else {
                                    layer.alert(data.msg, {icon: 2});
                                }
                            },
                            error: function () {
                                layer.alert('服务器错误', {icon: 2});
                            }
                        });
                    },
                    success: function () {
                        $("#RefundAmount").val(money - 0);
                        layui.use('slider', function () {
                            var slider = layui.slider;
                            //渲染
                            slider.render({
                                elem: '#slides'  //绑定元素
                                , input: true
                                , theme: "red"
                                , value: 100
                                , change: function (value) {
                                    let price = money * (value / 100);
                                    $("#Refund").text(price)
                                    $("#RefundAmount").val(price);
                                }
                            });
                        });
                        $('#RefundAmount').bind('input propertychange', function () {
                            $("#Refund").text($(this).val())
                        });
                    }
                });
            },
            finish(tid) {
                layer.open({
                    title: '温馨提示',
                    content: '关闭售后工单后，订单状态将自动调整为已完成,用户可以再次发起售后,是否需要调整？',
                    btn: ['关闭售后工单', '取消'],
                    icon: 3,
                    btn1: function () {
                        $.ajax({
                            type: "post",
                            url: "ajax.php?act=Tickets&type=Finish&id=" + tid,
                            dataType: "json",
                            success: function (data) {
                                if (data.code == 1) {
                                    layer.alert(data.msg, {
                                        icon: 1,
                                        title: '温馨提示',
                                        end: function () {
                                            App.initialization();
                                        }
                                    });
                                } else {
                                    layer.msg(data.msg, {
                                        icon: 2
                                    });
                                }
                            },
                            error: function () {
                                layer.alert('加载失败！');
                            }
                        });
                    }
                });
            },
            RemarkSet(oid, tid, remark) {
                if (remark == '点击配置') {
                    remark = '';
                }
                layer.prompt({
                    formType: 2,
                    value: remark,
                    title: '修改订单售后备注',
                    area: ['300px', '300px']
                }, function (value, index) {
                    App.RemarkSets(tid, oid, value);
                    layer.close(index);
                });
            },
            RemarkSets(tid, oid, content) {
                $.ajax({
                    type: "POST", url: './main.php?act=AfterRemarkSet', data: {
                        oid: oid, remark: content, tid: tid,
                    }, dataType: "json", success: function (data) {
                        if (data.code >= 1) {
                            App.AfterList();
                        } else {
                            layer.msg(data.msg, {icon: 2});
                        }
                    }
                });
            },
            Gd(id, name) {
                layer.open({
                    type: 2,
                    area: ['98%', '98%'],
                    title: '工单详情[' + name + ']',
                    content: 'tickets_code.php?id=' + id,
                    skin: 'layui-layer-rim',
                    id: 'OrderMark',
                    btn: false,
                    end: function () {
                        App.AfterList();
                    }
                });
            },
            AfterList() {
                let is = layer.msg('列表载入中，请稍后...', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST", url: './main.php?act=AfterList', data: {
                        page: App.page,
                        limit: App.limit,
                    }, dataType: "json", success: function (res) {
                        layer.close(is);
                        if (res.code == 1) {
                            App.Data = res.data;
                            App.count = res.count;
                        } else {
                            App.Data = [];
                            App.type = 1;
                        }
                    }, error: function () {
                        layer.msg('服务器异常！');
                    }
                });
            }, initialization(limit = -1) {
                this.page = 1;
                this.limit = (limit === -1 ? this.limit : limit);
                layui.use('laypage', function () {
                    var laypage = layui.laypage;
                    $.ajax({
                        type: "POST", url: './main.php?act=AfterList', data: {
                            page: App.page,
                            limit: App.limit,
                        }, dataType: "json", success: function (res) {
                            if (res.code == 1) {
                                laypage.render({
                                    elem: 'Page',
                                    count: res.count,
                                    theme: '#641ec6',
                                    limit: App.limit,
                                    limits: [10, 20, 30, 50, 100, 200],
                                    groups: 3,
                                    first: '首页',
                                    last: '尾页',
                                    prev: '上一页',
                                    next: '下一页',
                                    skip: true,
                                    layout: ['count', 'page', 'prev', 'next', 'limit', 'limits'],
                                    jump: function (obj) {
                                        App.page = obj.curr;
                                        App.limit = obj.limit;
                                        App.Data = res.data;
                                        if (App.count !== -1) {
                                            App.AfterList();
                                        }
                                        App.count = res.count;
                                    }
                                });
                            } else {
                                layer.alert(res.msg, {
                                    icon: 2
                                });
                            }
                        }, error: function () {
                            layer.msg('服务器异常！');
                        }
                    });
                });
            }
        }
    }).mount('#App');
    App.initialization();
</script>
</body>
</html>

