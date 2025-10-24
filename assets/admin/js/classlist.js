const App = Vue.createApp({
    data() {
        return {
            Data: [], type: -1,
        }
    }, methods: {
        BatchSetting(cid) {
            layer.open({
                title: "温馨提示",
                content: '请问您需要进行什么操作？',
                icon: 3,
                btn: ['批量替换商品图片', '将商品图片批量本地化', '取消'],
                btn1: function () {
                    App.ReplaceProductImages(cid);
                },
                btn2: function () {
                    App.BatchProductPictureTransfer(cid);
                }
            })
        },
        BatchProductPictureTransfer(cid) { //图片本地化
            //确认框
            layer.confirm('是否要将这个分类下的全部商品图片本地化？<hr>当您将商品图片本地化后，其他同行将无法根据图片地址信息找到上游货源，并且再也不会出现图片无法显示的问题。然而，请注意，如果您的主机或服务器带宽较小，可能会导致图片加载缓慢，从而影响用户体验。', {
                title: '商品图片本地化', icon: 3, area: ["300px"], btn: ['确认', '取消'], btn1: function () {
                    let is = layer.msg('操作中，请稍后...', {icon: 16, time: 9999999});
                    $.ajax({
                        type: "POST", url: './main.php?act=BatchProductPictureTransfer', data: {
                            array: [cid], type: 2
                        }, dataType: "json", success: function (res) {
                            layer.close(is);
                            if (res.code === 1) {
                                layer.alert(res.msg, {
                                    icon: 1
                                });
                            } else {
                                layer.alert(res.msg, {
                                    icon: 2
                                });
                            }
                        }
                    })
                }
            })
        }, ReplaceProductImages(cid) { //批量换图
            layer.prompt({
                title: '批量换图,每行一个图片链接',
                formType: 2,
            }, function (value, index, elem) {
                if (value === '') {
                    layer.msg('请输入图片链接！');
                    return;
                }
                let images = value.split("\n");
                if (images.length === 0) {
                    layer.msg('请输入图片链接！');
                    return;
                }
                layer.close(index);
                let is = layer.msg('操作中，请稍后...', {icon: 16, time: 9999999});
                $.ajax({
                    type: "POST", url: './main.php?act=ReplaceProductImages', data: {
                        array: [cid], type: 2, image: images
                    }, dataType: "json", success: function (res) {
                        layer.close(is);
                        if (res.code === 1) {
                            layer.alert(res.msg, {
                                icon: 1
                            });
                        } else {
                            layer.alert(res.msg, {
                                icon: 2
                            });
                        }
                    }
                })
            });
        },
        ResetSort() {
            //重置排序
            layer.confirm('确定要重置排序吗，重置后排序ID全部会变成0？', {
                btn: ['确定', '取消'],
                btn1: function () {
                    let is = layer.msg('重置中，请稍后...', {icon: 16, time: 9999999});
                    $.ajax({
                        type: "POST",
                        url: './main.php?act=ClassSortReset',
                        dataType: "json",
                        success: function (res) {
                            layer.close(is);
                            if (res.code == 1) {
                                App.ClassList();
                            } else {
                                layer.alert(res.msg, {
                                    icon: 2
                                });
                            }
                        },
                        error: function () {
                            layer.msg('服务器异常！');
                        }
                    });
                }
            })
        },
        Deployment(cid, SuborderNumber) {
            let id = $("#Tab_" + cid);
            if (id.text() === '展开(' + SuborderNumber + ')') {
                id.attr('class', 'layui-icon layui-icon-down');
                $(".Tab_" + cid).show(200);
                id.text('合上(' + SuborderNumber + ')');
            } else {
                id.attr('class', 'layui-icon layui-icon-right');
                $(".Tab_" + cid).hide(200);
                id.text('展开(' + SuborderNumber + ')');
            }
        },
        ClassPaySet(cid, index) {
            let is = layer.msg('加载中，请稍后...', {icon: 16, time: 9999999});
            $.ajax({
                type: "POST", url: './main.php?act=ClassPaySet', data: {
                    cid: cid, key: index,
                }, dataType: "json", success: function (res) {
                    layer.close(is);
                    if (res.code == 1) {
                        App.ClassList();
                    } else {
                        layer.alert(res.msg, {
                            icon: 2
                        });
                    }
                }, error: function () {
                    layer.msg('服务器异常！');
                }
            });
        }, ClassDelete(cid, name) {
            let content = `
<div class="layui-form">
  <input type="checkbox" value="1" name="ClassType"  title="当前分类" lay-skin="tag" checked>
  <input type="checkbox" value="2" name="ClassType" title="下级分类" lay-skin="tag"> 
  <input type="checkbox" value="3" name="ClassType" title="当前分类商品" lay-skin="tag"> 
  <input type="checkbox" value="4" name="ClassType" title="下级分类商品" lay-skin="tag"> 
</div>
<blockquote class="layui-elem-quote" style="margin-top: 1em">
  注意事项：<br>分类和商品删除后无法恢复，请谨慎操作！
  <br>当一级分类删除后，如果未删除下级分类，则下级分类会自动升级为一级分类！<br>
  如果商品失去了上级，则商品分类ID默认为-1，即为未分类！，可以在商品列表单独查看！
</blockquote>
            `;
            layer.open({
                title: '请选择需要删除的内容 - ' + name,
                content: content,
                btn: ['确认删除', '取消'],
                btn1: function () {
                    //获取选中的值
                    let type = [];
                    $("input:checkbox[name='ClassType']:checked").each(function () {
                        type.push($(this).val());
                    })
                    if (type.length === 0) {
                        layer.msg('请至少选择一项', {icon: 2});
                        return;
                    }
                    App.ClassDelGet(cid, name, type);
                },
                success: function () {
                    layui.use('form', function () {
                        let form = layui.form;
                        form.render();
                    });
                }
            });
        },
        ClassDelGet(cid, name, type) {
            let is = layer.msg('删除中，请稍后...', {icon: 16, time: 9999999});
            $.ajax({
                type: "POST", url: './main.php?act=ClassDelete', data: {
                    cid: cid, name: name, type: type,
                }, dataType: "json", success: function (res) {
                    layer.close(is);
                    if (res.code == 1) {
                        layer.alert(res.msg, {
                            icon: 1, btn1: function () {
                                App.ClassList();
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
        },
        SortSet(cid, _this) {
            let value = _this.value - 0;
            let is = layer.msg('加载中，请稍后...', {icon: 16, time: 9999999});
            $.ajax({
                type: "POST", url: './main.php?act=ClassSortSet', data: {
                    cid: cid, sort: value,
                }, dataType: "json", success: function (res) {
                    layer.close(is);
                    if (res.code == 1) {
                        App.ClassList();
                    } else {
                        layer.alert(res.msg, {
                            icon: 2
                        });
                    }
                }
            });
        }
        , ClassStateSet(cid, type, name) {
            let is = layer.msg('加载中，请稍后...', {icon: 16, time: 9999999});
            $.ajax({
                type: "POST", url: './main.php?act=ClassStateSet', data: {
                    cid: cid, type: type, name: name
                }, dataType: "json", success: function (res) {
                    layer.close(is);
                    if (res.code == 1) {
                        App.ClassList();
                    } else {
                        layer.alert(res.msg, {
                            icon: 2
                        });
                    }
                }, error: function () {
                    layer.msg('服务器异常！');
                }
            });
        }, ClassList() {
            let is = layer.msg('分类载入中，请稍后...', {icon: 16, time: 9999999});
            $.ajax({
                type: "POST", url: './main.php?act=ClassList', data: {
                    type: 2
                }, dataType: "json", success: function (res) {
                    layer.close(is);
                    App.type = 1;
                    if (res.code == 1) {
                        App.Data = res.data;
                        App.type = 1;
                    } else {
                        App.Data = [];
                    }
                }, error: function () {
                    layer.msg('服务器异常！');
                }
            });
        }
    }
}).mount('#App');
App.ClassList();