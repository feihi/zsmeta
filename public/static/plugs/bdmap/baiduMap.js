// 百度地图API功能
var default_city_name = "郑州";
if(typeof(map_Marker) != "undefined")
{
    if(map_Marker.city_name !="") default_city_name = map_Marker.city_name;
}
var map = new BMap.Map("l-map");
map.centerAndZoom(default_city_name,12); 
var geoc = new BMap.Geocoder();   
// 初始化地图,设置城市和地图级别。
map.enableScrollWheelZoom();  // 开启鼠标滚轮缩放    
map.enableKeyboard();         // 开启键盘控制    
map.enableContinuousZoom();   // 开启连续缩放效果    
map.enableInertialDragging(); // 开启惯性拖拽效果  

// 编辑时使用
if(typeof(map_Marker) != "undefined")
{
    //console.log(map_Marker);
    var point = new BMap.Point(map_Marker.longitude, map_Marker.latitude);    
    map.centerAndZoom(point, 15);    
    var marker = new BMap.Marker(point);        // 创建标注    
    map.addOverlay(marker);                     // 将标注添加到地图中 
}

map.addEventListener("click", showInfo);

var ac = new BMap.Autocomplete(    // 建立一个自动完成的对象
    {"input" : "search"
    ,"location" : map
});
ac.addEventListener("onhighlight", function(e) {  // 鼠标放在下拉列表上的事件
var str = "";
    var _value = e.fromitem.value;
    var value = "";
    if (e.fromitem.index > -1) {
        //value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
		value = _value.street +  _value.business;
    }    
    str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;
    
    value = "";
    if (e.toitem.index > -1) {
        _value = e.toitem.value;
        //value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
		value = _value.street +  _value.business;
    }
    if(typeof(map_Marker) != "undefined"){
        G(map_Marker.lng_lat_prefix+'longitude').value = '';
        G(map_Marker.lng_lat_prefix+'latitude').value = '';  
    }else{
        G('longitude').value = '';
        G('latitude').value = '';  
    }
    str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
    G("searchResultPanel").innerHTML = str;
});

var myValue;
ac.addEventListener("onconfirm", function(e)
{    
    // 鼠标点击下拉列表后的事件
    var _value = e.item.value;
    // myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
	myValue = _value.street +  _value.business;
    G("searchResultPanel").innerHTML = "onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue; 
    G("address").value = myValue;
    G("full_address").value = _value;
    G("full_address_json").value = JSON.stringify(_value);
    setPlace();
});

G('search').addEventListener('keydown', function(e){
    if(e.which == 13)
    {
        search();
    }
});

function G(id)
{
    if(document.getElementById(id) == null)
    {
        if (parent.document.getElementById(id) == null)
        {
            console.log('非弹窗展示...');
            return false;
        }
        return parent.document.getElementById(id)
    }
    return document.getElementById(id);
}


function search()
{
    var s = G("search").value;
    var local = new BMap.LocalSearch(map, { renderOptions: { map: map, autoViewport: true} });
    function myFun(){
        var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
        if(typeof(map_Marker) != "undefined"){
            G(map_Marker.lng_lat_prefix+'longitude').value = pp.lng;
            G(map_Marker.lng_lat_prefix+'latitude').value = pp.lat;
        }else{
            G('longitude').value = pp.lng;
            G('latitude').value = pp.lat;
        }
        map.centerAndZoom(pp, 18);
        map.addOverlay(new BMap.Marker(pp));    //添加标注
    }
    local.search(s); // 查找城市
}

function showInfo(e)
{
    map.clearOverlays();
    marker = new BMap.Marker(new BMap.Point(e.point.lng, e.point.lat));  // 创建标注
    var pt = e.point;
    console.log(pt);
    geoc.getLocation(pt, function(rs){
        var addComp = rs.addressComponents;
        G('full_address').value = addComp.province  + addComp.city  + addComp.district  + addComp.street  + addComp.streetNumber;
        G('full_address_json').value = JSON.stringify(addComp);
        G('address').value = addComp.street  + addComp.streetNumber;
        if(typeof(map_back_func)=="function"){
            map_back_func(addComp.city);
        }
        
        if(typeof(map_back_func_hotel)=="function"){
            map_back_func_hotel(addComp.city,pt);
        }
        
    });

    map.addOverlay(marker);
    //获取经纬度
    if(typeof(map_Marker) != "undefined"){
        G(map_Marker.lng_lat_prefix+'longitude').value = pt.lng;
        G(map_Marker.lng_lat_prefix+'latitude').value = pt.lat;
    }else{
        G('longitude').value = pt.lng;
        G('latitude').value = pt.lat;
    }
}

function setPlace()
{
    map.clearOverlays();    //清除地图上所有覆盖物
    function myFun(){
        var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果
        // G('longitude').value = pp.lng;
        // G('latitude').value = pp.lat;
        geoc.getLocation(pp, function(rs){
            var addComp = rs.addressComponents;
            //G('address').value = addComp.province  + addComp.city  + addComp.district  + addComp.street  + addComp.streetNumber;
			// G('address').value = addComp.street  + addComp.streetNumber;
			if(typeof(map_back_func)=="function"){
				map_back_func(addComp.city);
			}
            
            if(typeof(map_back_func_hotel)=="function"){
				map_back_func_hotel(addComp.city,pp);
			}
            
        });
        map.centerAndZoom(pp, 18);
        map.addOverlay(new BMap.Marker(pp));    //添加标注
    }
    var local = new BMap.LocalSearch(map, { //智能搜索
      onSearchComplete: myFun
    });
    local.search(myValue);
}

function pickup()
{
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}

