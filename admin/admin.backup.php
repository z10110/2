<?php
/**
 * 数据备份功能扩展
 */
$title = '数据备份';
include 'header.php';
?>
<div class="row" id="App">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <a title="备份数据" href="javascript:App.backup();" class="badge badge-primary"><i
                        class="layui-icon layui-icon-addition"></i></a>
                <a title="初始化" href="javascript:App.initialization();" class="badge badge-warning mdui-m-l-1"><i
                        class="layui-icon layui-icon-refresh-3"></i></a>
                <a title="上传数据文件" href="javascript:" id="Uploading"
                   class="badge badge-dark mdui-text-color-white mdui-m-l-1"><i
                        class="layui-icon layui-icon-upload"></i></a>
                <span class="mdui-m-l-1">共:{{count}}条备份</span>
            </div>
            <div class="card-body m-t-0" style="overflow-y: auto" v-if="count>=1">
                <table id="table" class="table table-hover table-centered mb-0" style="font-size:0.9em;">
                    <thead style="white-space: nowrap">
                    <tr>
                        <th>操作</th>
                        <th>备份时间</th>
                        <th>程序版本</th>
                        <th>表总数</th>
                        <th>数据总数</th>
                        <th>备份文件名称</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item,index) in Data">
                        <td>
                            <span @click="RestoreBackup(item.name)" class="badge badge-success mr-1"
                                  style="cursor:pointer;">恢复</span>
                            <span @click="DeleteBackup(item.name)" class="badge badge-danger mr-1"
                                  style="cursor:pointer;">删除</span>
                            <a :download="item.name"
                               :href="'<?= href(2) . ROOT_DIR . 'includes/extend/log/SQL/' ?>'+item.name"
                               class="badge badge-secondary mr-1">下载</a>
                        </td>
                        <td>
                            {{item.CreationTime}}
                        </td>
                        <td>
                            <span class="badge badge-warning-lighten">{{item.ProgramVersion}}</span>
                        </td>
                        <td>
                            <span class="badge badge-success-lighten">{{item.StructureCount}}</span>
                        </td>
                        <td>
                            <span class="badge badge-primary-lighten">{{item.InstalDataCount}}</span>
                        </td>
                        <td>
                            {{item.name}}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="card-body m-t-0">
                没有可以恢复的备份，请先去备份或上传备份文件！
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
                Data: [],
                count: 0,
            }
        }, methods: {
            initialization() {
                let that = this;
                $.ajax({
                    url: './main.php?act=BackupList',
                    type: 'post',
                    dataType: 'json',
                    success(res) {
                        if (res.code === 1) {
                            that.Data = res.data;
                            that.count = res.count;
                        } else {
                            that.Data = [];
                            that.count = 0;
                            layer.msg(res.msg, {icon: 2});
                        }
                    }
                })
            },
            DeleteBackup(name) {
                layer.open({
                    title: '温馨提示',
                    content: '确定要删除备份数据吗，删除后不可恢复！',
                    icon: 3,
                    btn: ['确认删除', '取消'],
                    btn1: function () {
                        layer.msg('正在删除中，请稍后...', {icon: 16, shade: 0.01, time: 99999999});
                        $.ajax({
                            url: './main.php?act=BackupDelete',
                            type: 'post',
                            data: {
                                name: name,
                            },
                            dataType: 'json',
                            success(res) {
                                if (res.code === 1) {
                                    layer.msg(res.msg, {icon: 1});
                                    App.initialization();
                                } else {
                                    layer.msg(res.msg, {icon: 2});
                                }
                            }
                        })
                    }
                });
            },
            RestoreBackup(name) {
                layer.open({
                    title: '温馨提示',
                    content: '确定要恢复备份数据吗，恢复数据会覆盖当前数据！<br>如果是历史备份数据，可能会出现版本兼容问题！<br>如果数据对应的程序版本不一致，请恢复备份后前往->数据中心->系统升级界面校准数据文件！<br>恢复历史备份数据会使当前登录状态失效，重新登录即可！',
                    icon: 3,
                    btn: ['确认恢复', '取消'],
                    btn1: function () {
                        layer.msg('正在恢复中，请稍后...', {icon: 16, shade: 0.01, time: 99999999});
                        $.ajax({
                            url: './main.php?act=BackupRestore',
                            type: 'post',
                            data: {
                                name: name,
                            },
                            dataType: 'json',
                            success(res) {
                                if (res.code === 1) {
                                    const jsonString = JSON.stringify(res.data, null, '\t');
                                    layer.alert(res.msg + '<hr><pre  class="layui-code code-demo">' + jsonString + '</pre>', {
                                        icon: 1, end: function () {
                                            window.location.reload();
                                        }
                                    });
                                } else {
                                    layer.msg(res.msg, {icon: 2});
                                }
                            }
                        })
                    }
                });
            },
            backup() {
                let that = this;
                layer.open({
                    title: '温馨提示',
                    content: '确定要备份站点数据吗，备份速度取决于服务器速度和数据量大小！',
                    icon: 3,
                    btn: ['确定', '取消'],
                    btn1: function () {
                        layer.msg('正在备份中，请稍后...', {icon: 16, shade: 0.01, time: 99999999});
                        $.ajax({
                            url: './main.php?act=BackupAdd',
                            type: 'post',
                            dataType: 'json',
                            success(res) {
                                if (res.code === 1) {
                                    layer.alert(res.msg, {icon: 1});
                                    that.initialization();
                                } else {
                                    layer.msg(res.msg, {icon: 2});
                                }
                            }
                        })
                    }
                });
            }
        }
    }).mount('#App');
    App.initialization();
    layui.use('upload', function () {
        layui.upload.render({
            elem: '#Uploading',
            url: './main.php?act=BackupUpload',
            accept: 'file',
            done: function (res) {
                layer.msg(res.msg, {icon: res.code === 1 ? 1 : 2});
                App.initialization();
            },
            error: function (res) {
                layer.alert(res.msg, {
                    title: '文件上传失败', icon: 2,
                });
            }
        });
    });
</script>
