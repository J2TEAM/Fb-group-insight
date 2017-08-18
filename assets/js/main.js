rankData = new Vue({
  el: "#rankTable",
  data: {
    full: null,
    badges: [
      "ChallengerBadge.png",
      "GoldBadgeSeason.png",
      "DiamondBadge.png",
      "PlatinumBadgeSeason.png",
      "SilverBadgeSeason.png",
      "MasterBadge.png",
      "BronzeBadgeSeason.png",
      "UnrankedBadge.png",
    ],
    shouldDrawChart: false,
    chart: null
  },
  methods: {
    getData() {
      fetch("./full.json")
        .then(resp => resp.json())
        .then(json => {
          this.full = json;
          this.drawChart();
          this.shouldDrawChart = true;
        });
    },
    dateFormat(date) {
      d = date.getDate()
      m = date.getMonth()+1
      y = date.getFullYear()
      h = date.getHours()
      mi = date.getMinutes()
      s = date.getSeconds()
      return `${d<10?"0"+d:d}/${m<10?"0"+m:m}/${y} ${h<10?"0"+h:h}:${mi<10?"0"+mi:mi}:${s<10?"0"+s:s}`;
    },
    drawChart() {
      if (!this.shouldDrawChart) return;
      try {
        let colors = ["#3C8AB8", "#005F97", "#004C79"];
        let colors1 = ["#f1c40f", "#e67e22", "#e74c3c", "#f39c12", "#d35400", "#c0392b"];
        let options = {
          // theme: 'maximized'
          chartArea: {
            right: '4%',
            top: '11%',
            width: '84%',
            height: '75%',
          },
          animation: {
            duration: 800,
            easing: 'out',
            startup: true
          },
          legend: {
            position: 'none'
          },
          axisTitlesPosition: 'none',
          colors: ["#2980b9"]
        }
        let table = [
          ['Timestamp', 'Members' /*, { role: 'style' }*/ ]
        ];

        this.full.engagement.daily_active_member_count.forEach((day, index) => {
          table.push([new Date(day.time * 1000), day.value /*, colors1[index%colors1.length]*/ ]);
        });

        let data = google.visualization.arrayToDataTable(table);

        let chart = new this.google.visualization.ColumnChart(document.getElementById('activeMemberCountChart'));
        chart.draw(data, options);

        // var chart = new this.google.charts.Bar(document.getElementById('postCountChart'));
        // chart.draw(data, google.charts.Bar.convertOptions(options));

        let weekly_activity = this.full.engagement.weekly_activity_breakdown;

        let table2 = [
          ["Source", "Amount", {
            role: 'style'
          }]
        ];

        weekly_activity.forEach((source, index) => {
          table2.push([source.name, source.value, colors[index % colors.length]]);
        });
        // console.log(table2);
        let data2 = google.visualization.arrayToDataTable(table2);

        let chart2 = new this.google.visualization.ColumnChart(document.getElementById('weeklyActivityChart'));
        chart2.draw(data2, options);

        // let join_sources = this.full.growth.daily_member_join_source_breakdown;

        // let table2 = [
        //   ["Source", "Amount"]
        // ];

        // join_sources.forEach(source => {
        //   table2.push([source.name, source.value]);
        // });
        // console.log(table2);
        // var data2 = google.visualization.arrayToDataTable(table2);

        // var chart2 = new this.google.visualization.ColumnChart(document.getElementById('memberJoinSourceChart'));
        // chart2.draw(data2);
      } catch (e) {
        setTimeout(this.drawChart, 1000);
      }
    }
  },
  mounted() {
    this.getData();
    this.google = google;
    this.google.charts.load('current', {
      packages: ['corechart', 'bar']
    });
    this.google.charts.setOnLoadCallback(() => {
      this.drawChart();
      this.shouldDrawChart = true;
    });
  }
});