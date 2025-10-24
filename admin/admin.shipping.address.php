<?php
$title = '用户收货地址 [用户后台自行添加]';
include 'header.php';
?>
<div class="row" id="App">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                共:{{count}}个配置
            </div>
            <div class="card-header">
                注意：商品的下单信息请严格按照以下格式填写，否则无法正常匹配收货地址信息！<br>
                <span style="color: red">姓名|电话|收货地址</span>,此功能仅对部分模板生效!
            </div>
            <div class="card-body m-t-0" style="overflow-y: auto;min-height: 50vh;">
                <table id="table" class="table table-hover table-centered mb-0" style="font-size:0.9em;">
                    <thead style="white-space: nowrap">
                    <tr>
                        <th>操作</th>
                        <th>编号</th>
                        <th>用户ID</th>
                        <th>姓名</th>
                        <th>电话</th>
                        <th>地区</th>
                        <th>详细地址</th>
                        <th>创建时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item,index) in Data">
                        <td>
                            <button @click="Del(item.id)"
                                    class="layui-btn layui-bg-red layui-btn-sm">
                                删除地址
                            </button>
                        </td>
                        <td>
                            {{ item.id }}
                        </td>
                        <td>
                            <a :href="'./admin.user.log.php?uid='+item.uid" target="_blank"
                               class="badge badge-primary-lighten">
                                {{ item.uid }}
                            </a>
                        </td>
                        <td>
                            {{ item.name }}
                        </td>
                        <td>
                            {{ item.phone }}
                        </td>
                        <td>
                            {{ item.region }}
                        </td>
                        <td>
                            {{ item.address }}
                        </td>
                        <td>
                            {{ item.addtime }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-if="Data.length===0" class="text-center w-100 mt-3 font-w300">
                    {{ type==-1?'正在载入中,请稍后...':'一个收货地址信息也没有' }}
                </div>
            </div>
            <div class="layui-card-body" style="text-align:center;">
                <div id="Page"></div>
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
                Data: [], page: 1, limit: 12, count: -1, status: 1,
            }
        }, methods: {
            //删除记录
            Del(id) {
                layer.confirm('确定删除该地址信息吗？', {
                    btn: ['确定', '取消']
                }, function () {
                    let is = layer.msg('正在删除中，请稍后...', {icon: 16, time: 9999999});
                    $.ajax({
                        type: "POST", url: './main.php?act=ReceivingDel', data: {
                            id: id
                        }, dataType: "json", success: function (res) {
                            layer.close(is);
                            if (res.code == 1) {
                                layer.msg(res.msg, {icon: 1});
                                App.initialization();
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
            },
            ReceivingList() {
                let is = layer.msg('列表载入中，请稍后...', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST", url: './main.php?act=ReceivingList', data: {
                        page: App.page,
                        limit: App.limit,
                        status: App.status,
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
                        type: "POST", url: './main.php?act=ReceivingList', data: {
                            page: App.page,
                            limit: App.limit,
                            status: App.status,
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
                                            App.ReceivingList();
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

    layui.use(['upload', 'laydate'], function () {
        App.laydate = layui.laydate;
    });
</script>
