
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link type="text/css" rel="stylesheet" href="css/reset.css">
    <link type="text/css" rel="stylesheet" href="css/simplePagination.css"/>
    <link type="text/css" rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&family=Nunito:wght@300;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="jquery/jquery.simplePagination.js"></script>
</head>
<body>
    <div class="google-map">
    <iframe src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d3240.4999108696134!2d139.69005116505537!3d35.68931383713639!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1z6YO95bqB!5e0!3m2!1sja!2sjp!4v1680563281018!5m2!1sja!2sjp" width="1920" height="1440" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

    <div class="wrapper">
        <div class="titlearea">
            <p class="title">RestaurantMap</p>
            <p class="subtitle">近くのお店を探そう</p>
            <form onsubmit="return false" class="form">
                <button class="button" value="1">300m</button><button class="button" value="2">500m</button><button class="button" value="3">1km</button><button class="button" value="4">2km</button><button class="button" value="5">3km</button><input type="hidden" class="hidden" value="">
            </form>
        </div>

        <template class="template">
            <a href="#" class="link">
                <div class="shop">
                    <img class="thumbnail" src=""></img>
                    <p class="name"></p>
                    <p class="access"></p>
                </div>
            </a>
        </template>
        <div class="serch-area">
            <div class="container"></div>
            <div class="pager"></div>
        </div>

        <template class="modaltemplate">
            <div class="modal-shop">
                <div class="modal-header"><span class="modalClose">×</span></div>
                <div class="modal-body">
                    <img class="photo" src=""></img>
                    <p class="name"></p>
                    <p class="address"></p>
                    <p class="open"></p>
                </div>
            </div>
        </template>
        <div class="modalarea"></div>
    </div>
<script>
    const template = document.querySelector(".template");
    const container = document.querySelector(".container");

    const modaltemplate = document.querySelector(".modaltemplate");
    const modalarea = document.querySelector(".modalarea");
    let position_lat;
    let position_lon;

    if (navigator.geolocation) {
        const option = {
          enableHighAccuracy: true,
          maximumAge: 0,
        };
        const success = (position) => {
          console.log(position);
          position_lat = position.coords.latitude;
          position_lon = position.coords.longitude;
        };
        const error = (error) => {
          console.error(error);
        };
        navigator.geolocation.getCurrentPosition(success, error, option);
    } else {
        console.log("Geolocation not supported");
    }

    let response;

    document.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelector('.hidden').value = btn.value;
            document.querySelector('.wrapper').classList.add('slide-up');
            sendApi();
        });
    });
    
    const setPagination = () => {
        let responseitems = response.available;
        let responsestart = (response.start+9)/10;
        console.log(response.start);
        $(function() {
            $('.pager').pagination({
                items: responseitems,
                itemsOnPage: 12,
                currentPage: responsestart,
                cssStyle: 'light-theme',
                prevText: '«',
                nextText: '»',
                onPageClick: function (currentPageNumber) {
                    sendApi(currentPageNumber);
                }
            });
        });
    }
    
    const updateShopList = (shop) => {
        container.innerHTML = '';
        for (var i = 0; i < response.shop.length; i++) {
            let shop = template.content.cloneNode(true);
            shop.querySelector(".name").textContent = response.shop[i].name;
            shop.querySelector(".access").textContent = response.shop[i].access;
            shop.querySelector(".thumbnail").src = response.shop[i].thumbnail;
            shop.querySelector(".link").id = "shop" + (i + 1);
            container.appendChild(shop);
        }
        setPagination();
    }
    
    const sendApi = (currentPageNumber) => {
        let position_data;
        if(currentPageNumber){
            position_data = {
                'lat': position_lat,
                'lon': position_lon,
                'range': document.querySelector('.hidden').value,
                'currentPageNumber': currentPageNumber,
            }
        }else {
            position_data = {
                'lat': position_lat,
                'lon': position_lon,
                'range': document.querySelector('.hidden').value,
            }
        }
       
        const url = "serch.php";
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        xhr.onreadystatechange = function() {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    response = JSON.parse(xhr.responseText);
                    console.log(response);
                    updateShopList(response);
                } else {
                    if (xhr.status) {
                        console.error("Ajax request error: status " + xhr.status);
                    } else {
                        console.error("Ajax request error");
                    }
                }
            } 
        };
        const json = JSON.stringify(position_data);
        xhr.send(json);
    }
</script>
</body>
</html>