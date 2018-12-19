/* 时间范围选择 */
window.dateRangepickerConfig = {
    locale: {
        opens: 'left',
        drops: 'down',
        format: 'YYYY-MM-DD HH:mm:ss',
        separator: "~",
        applyLabel: "确定",
        cancelLabel: "取消",
        fromLabel: "起始时间",
        toLabel: "结束时间'",
        customRangeLabel: "自定义",
        weekLabel: "周",
        daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
        monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
        firstDay: 1
    },
    autoApply: true,
    timePicker: true,
    timePickerSeconds: true,
    timePickerIncrement: 1,
    timePicker24Hour: true,
    alwaysShowCalendars: true,
    showWeekNumbers: true,
    showISOWeekNumbers: true,
    startDate: moment().startOf('day'),
    endDate: moment().startOf('day'),
    //汉化按钮部分
    ranges: {
        '今日': [moment().startOf('day'), moment().endOf('day')],
        '昨日': [moment().startOf('day').subtract(1, 'days'), moment().endOf('day').subtract(1, 'days')],
        '最近7日': [moment().subtract(6, 'days'), moment()],
        '最近30日': [moment().subtract(29, 'days'), moment()],
        '本月': [moment().startOf('month'), moment().endOf('month')],
        '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
};