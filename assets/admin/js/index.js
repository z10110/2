const Announcement = Vue.createApp({
    data() {
        return {}
    }, mounted() {
        this.GetList();
    }, methods: {
        GetList(type = 1) {
            $.ajax({
                type: "POST", url: './main.php?act=CircuitNotice', data: {
                    type: type
                }, dataType: "json", success: function (res) {
                    layer.closeAll('dialog');
                    layer.closeAll('iframe');
                    layer.closeAll('loading');
                    layer.closeAll('tips');
                    if (res.code == 1) {
                        vm.Data2 = res.data;
                    } else {
                        layer.alert(res.msg, {
                            icon: 2
                        });
                    }
                }, error: function () {
                    //layer.msg('服务器异常！');
                    vm.Data2 = [];
                }
            });
        }
    },
}).mount('#Announcement');

const vm = Vue.createApp({
    data() {
        return {
            Data: -1, SalesList: -1, type: 1, date: '', Data2: -1,
        }
    }, methods: {
        Details(index) {
            let res = this.Data2[index];
            mdui.dialog({
                title: res.title,
                content: '<link href="../assets/css/Global.css" rel="stylesheet" type="text/css"/><div class="editor">' + res.content + '<hr>发布者：' + res.issuer + '<br>发布时间：' + res.date + '<br>阅读次数：' + res.read + '</div>',
                modal: true,
                history: false,
                buttons: [{
                    text: '关闭',
                }]
            });
        }, SalesListGet(type = 1) {
            this.type = type;
            let _this = this;
            $.ajax({
                type: "POST", url: './main.php?act=SalesChart', data: {
                    type: type,
                }, dataType: "json", success: function (data) {
                    if (data.code == 1) {
                        _this.SalesList = data.data;
                    } else {
                        _this.SalesList = [];
                    }
                }
            });
        }, DataAnalysis(type = 1) {
            let _this = this;
            this.SalesListGet(1);
            let is = layer.msg('数据获取中...', {icon: 16, time: 9999999});
            this.Data = -1;
            $.ajax({
                type: "POST", url: './main.php?act=DataStatistics', data: {
                    type: type, date: _this.date,
                }, dataType: "json", success: function (data) {
                    layer.close(is);
                    if (data.code == 1) {
                        _this.Data = data.data;
                        const Date = [];
                        const OrderSum = [];
                        const TurnoverSum = [];
                        const NewUser = [];
                        for (const Key in data.data.Table) {
                            const Value = data.data.Table[Key]
                            Date[Key] = Value.date;
                            OrderSum[Key] = Value.OrderSum;
                            TurnoverSum[Key] = Value.TurnoverSum;
                            NewUser[Key] = Value.NewUser;
                        }
                        Ru((data.data.Table).length, Date, OrderSum, TurnoverSum, NewUser);
                    } else {
                        layer.msg(data.msg, {icon: 2});
                    }
                }
            });
        },
        LocationDetection() {
            const LocationDetection = layui.data('LocationDetection');
            if (LocationDetection.type === '1') {
                return;
            }
            $.ajax({
                type: "POST",
                url: './main.php?act=LocationDetection',
                dataType: "json",
                success: function (data) {
                    let content = '';
                    switch (data.code) {
                        case -2: //未知
                            content = '无法检测到您服务器所在地，如果您的服务器所在地是在国外，可以开启静态资源加速，防止前台模板乱码，以及提高网站前台访问速度！';
                            break;
                        case -1://国外
                            content = '检测到您服务器所在地在国外：<span style="color: red">' + data.msg + '</span>，可以开启静态资源加速，防止前台模板乱码，并提高网站前台访问速度！';
                            break;
                        default: //国内
                            return true;
                    }
                    mdui.dialog({
                        title: '温馨提示',
                        content: content + '<hr>您可以在：站点配置->网站模板配置->全局配置->晴玖静态资源加速处 开启加速！',
                        modal: true,
                        history: false,
                        buttons: [{
                            text: '关闭',
                        }, {
                            text: '不再提醒', onClick: function () {
                                layui.data('LocationDetection', {
                                    key: 'type', value: '1'
                                });
                            }
                        }, {
                            text: '前往开启', onClick: function () {
                                layui.data('LocationDetection', {
                                    key: 'type', value: '1'
                                });
                                window.open('./admin.template.set.php');
                            }
                        }]
                    });
                }
            });
        }
    }
}).mount('#App');

function Ru(Sum, Date, OrderSum, TurnoverSum, NewUser) {
    const option = {
        title: {
            text: '近' + Sum + '天', textStyle: {
                color: '#0e002d', fontWeight: 'bolder', fontSize: 14,
            }
        }, tooltip: {
            trigger: 'axis'
        }, legend: {
            data: ['订单数', '交易额', '注册数'], show: true, type: 'scroll', right: '10%', pageButtonPosition: 'end',
        }, grid: {
            left: '3%', right: '4%', bottom: '3%', containLabel: true, show: true,
        }, toolbox: {
            feature: {
                saveAsImage: {}
            }
        }, xAxis: {
            type: 'category', boundaryGap: false, data: Date
        }, yAxis: {
            type: 'value'
        }, series: [{
            name: '订单数', type: 'line', stack: '条', data: OrderSum
        }, {
            name: '交易额', type: 'line', stack: '元', data: TurnoverSum
        }, {
            name: '注册数', type: 'line', stack: '元', data: NewUser
        }]
    };
    echarts.init(document.getElementById("container")).setOption(option);
}

layui.use('laydate', function () {
    let Data = new Date();
    vm.date = Data.getFullYear() + '-' + ((Data.getMonth() + 1) < 10 ? '0' + (Data.getMonth() + 1) : (Data.getMonth() + 1)) + '-' + Data.getDate();
    var laydate = layui.laydate;
    laydate.render({
        elem: '#HomeDate', value: vm.date, max: vm.date, theme: '#727cf5', done: function (value) {
            vm.date = value;
            vm.DataAnalysis(1);
        }
    });
});
vm.DataAnalysis();
vm.LocationDetection();
