function qqMapInit(id, autocompleteId, locationId) {
    document.getElementById(id).style.width = '100%';
    document.getElementById(id).style.height = '280px';
    var map = new qq.maps.Map(document.getElementById(id), {zoom: 11});
    //设置城市信息查询服务
    var citylocation = new qq.maps.CityService();
    //请求成功回调函数
    citylocation.setComplete(function (result) {
        map.setCenter(result.detail.latLng);
    });
    citylocation.searchLocalCity();
    //实例化自动完成
    var ap = new qq.maps.place.Autocomplete(document.getElementById(autocompleteId));
    //调用Poi检索类。用于进行本地检索、周边检索等服务。
    var searchService = new qq.maps.SearchService({
        complete: function (results) {
            if (results.type === "CITY_LIST") {
                alertNotify("当前检索结果分布较广，请指定城市进行检索", 'danger');
                return;
            }
            var pois = results.detail.pois;
            var latlngBounds = new qq.maps.LatLngBounds();
            try {
                setLocation(map, pois[0].latLng, locationId, pois[0].name);
            } catch (e) {
                alertNotify("检索失败", 'danger');
            }
            for (var i = 0, l = pois.length; i < l; i++) {
                var poi = pois[i];
                latlngBounds.extend(poi.latLng);
            }
            map.fitBounds(latlngBounds);
        }
    });
    //添加监听事件
    qq.maps.event.addListener(ap, "confirm", function (res) {
        searchService.search(res.value);
    });
    qq.maps.event.addListener(map, 'click', function (event) {
        setLocation(map, event.latLng, locationId, '');
    });
}

function setLocation(map, location, locationId, title) {
    document.getElementById(locationId).value = location.getLng() + ',' + location.getLat();
    var marker = new qq.maps.Marker({
        map: map,
        position: location
    });
    if (typeof title !== "undefined" && title !== '' && title !== null) {
        marker.setTitle(title);
    }
}