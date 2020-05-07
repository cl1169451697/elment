<template>
  <div class="one">
    <div id="main" style="width: 600px;height:400px;"></div>
    <div id="bar" style="width: 600px;height:400px;"></div>
    <div id="weather" style="width: 600px;height:400px;"></div>
  </div>
</template>
<script>
import echarts from "echarts";
export default {
  data() {
    return {};
  },
  created() {},
  // dom事件渲染完成之后执行
  mounted() {
    this.init();
    this.mybar_type();
    this.weather()
  },
  methods: {
    init() {
      // 基于准备好的dom，初始化echarts实例
      var myChart = echarts.init(document.getElementById("main"));

      // 指定图表的配置项和数据
      var option = {
        title: {
          text: "ECharts 入门示例",
        },
        tooltip: {},
        legend: {
          data: ["销量"],
        },
        xAxis: {
          data: ["衬衫", "羊毛衫", "雪纺衫", "裤子", "高跟鞋", "袜子"],
        },
        yAxis: {},
        series: [
          {
            name: "销量",
            type: "bar",
            data: [5, 20, 36, 10, 10, 20],
          },
        ],
      };

      // 使用刚指定的配置项和数据显示图表。
      myChart.setOption(option);
    },

    mybar_type() {
      var myChart = echarts.init(document.getElementById("bar"));

      // 指定图表的配置项和数据
      var option = {
        xAxis: {
          type: "category",
          data: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        },
        yAxis: {
          type: "value",
        },
        series: [
          {
            data: [820, 932, 901, 934, 1290, 1330, 1320],
            type: "line",
          },
        ],
      };
       // 使用刚指定的配置项和数据显示图表。
      myChart.setOption(option);
    },

    weather(){
        var myChart = echarts.init(document.getElementById("weather"));
 
      // 指定图表的配置项和数据
      var option = {
    tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b}: {c} ({d}%)'
    },
    legend: {
        orient: 'vertical',
        left: 10,
        data: ['直接访问', '邮件营销', '联盟广告', '视频广告', '搜索引擎']
    },
    series: [
        {
            name: '访问来源',
            type: 'pie',
            radius: ['50%', '70%'],
            avoidLabelOverlap: false,
            label: {
                show: false,
                position: 'center'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: '30',
                    fontWeight: 'bold'
                }
            },
            labelLine: {
                show: false
            },
            data: [
                {value: 335, name: '直接访问'},
                {value: 310, name: '邮件营销'},
                {value: 234, name: '联盟广告'},
                {value: 135, name: '视频广告'},
                {value: 1548, name: '搜索引擎'}
            ]
        }
    ]
};
       // 使用刚指定的配置项和数据显示图表。
      myChart.setOption(option);
    }

  },
};
</script>
<style lang="scss" scoped>
.one div{
    display: inline-block;
}
</style>
