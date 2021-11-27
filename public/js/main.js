
function getCurrentTime() {
    return moment().format('h:mm A');
}
function getCurrentDateTime() {
    return moment().format('MM/DD/YY h:mm A');
}
function dataFormat(datetime) {
        return moment(datetime,'MM/DD/YY HH:mm:ss A').format('MM/DD/YY h:mm A');
}
function timeFormat(datetime) {
        return moment(datetime,'YY/MM/DD HH:mm:ss A').format(' h:mm A');

}
