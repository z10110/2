<?php
//退货地址
$title = '退货地址';
include 'header.php';
?>
<div class="row" id="App">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                共:{{count}}个配置 <span @click="Address()" class="layui-btn layui-btn-xs ml-1 layui-btn-normal"
                                         style="cursor: pointer;">创建新地址</span>
            </div>
            <div class="card-header">
                用户退货时，您可以从此处选择一个地址来让用户进行退货！
            </div>
            <div class="card-body m-t-0" style="overflow-y: auto;min-height: 50vh;">
                <table id="table" class="table table-hover table-centered mb-0" style="font-size:0.9em;">
                    <thead style="white-space: nowrap">
                    <tr>
                        <th>操作</th>
                        <th>编号</th>
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
                    {{ type==-1?'正在载入中,请稍后...':'一个退货地址信息也没有' }}
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
<script src="../assets/js/distpicker.min.js"></script>
<script src="../assets/js/vue3.js"></script>
<script>
    const App = Vue.createApp({
        data() {
            return {
                Data: [], page: 1, limit: 12, count: -1, status: 2,
            }
        }, methods: {
            Address() {
                let content = `
		<div id="distpicker" style="width:300px;">
		  <select id="site_1" style="width: 100%;"></select>
		  <select id="site_2" style="width: 100%;"></select>
		  <select id="site_3" style="width: 100%;"></select>
		</div>
		<div style="width:300px;margin-top:10px">
			<textarea  class="layui-textarea" id="site_4" placeholder="请填写详细地址" />
		</div>
		`;
                layer.open({
                    title: '选择退货地址', content: content, btn: ['确定', '取消'], btn1: function () {
                        //需执行的操作
                        let province = $("#site_1").val();
                        let city = $("#site_2").val();
                        let district = $("#site_3").val();
                        let detail = $("#site_4").val();
                        App.Add(province + ',' + city + ',' + district, detail);
                    }, success: function () {
                        $('#distpicker').distpicker({
                            province: '-- 所在省 --', city: '-- 所在市 --', district: '-- 所在区 --'
                        });
                    }
                })
            },
            Add(region = '', address) {
                let content = `
<div class="layui-form layui-form-pane">
    <div class="layui-form-item">
        <label class="layui-form-label">姓名</label>
        <div class="layui-input-block">
          <input type="text" id="name" placeholder="请填写收货人姓名"  class="layui-input">
        </div>
      </div>
    <div class="layui-form-item">
        <label class="layui-form-label">电话</label>
        <div class="layui-input-block">
          <input type="text" id="phone" placeholder="请填写收货人手机号"  class="layui-input">
        </div>
      </div>
    <div class="layui-form-item">
        <label class="layui-form-label">所在地区</label>
        <div class="layui-input-block">
          <input type="text" id="region" value="` + region + `" placeholder="省市区县、乡镇等"  class="layui-input">
        </div>
      </div>
    <div class="layui-form-item">
        <label class="layui-form-label">详细地址</label>
        <div class="layui-input-block">
           <textarea id="address" placeholder="街道，楼牌等" class="layui-textarea">` + address + `</textarea>
        </div>
      </div>
</div>
                `;
                layer.open({
                    title: '创建新地址',
                    shadeClose: true,
                    shade: 0.8,
                    content: content,
                    btn: ['确认创建', '取消'],
                    btn1: function () {
                        let is = layer.msg('正在删除中，请稍后...', {icon: 16, time: 9999999});
                        $.ajax({
                            type: "POST", url: './main.php?act=ReceivingAdd', data: {
                                name: $("#name").val(),
                                phone: $("#phone").val(),
                                region: $("#region").val(),
                                address: $("#address").val(),
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
                    }
                });
            },
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
