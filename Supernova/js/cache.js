// function set_localcache(key, value) {
//     // key = CryptoJS.MD5(key).toString();
//     remove_localcache(key);
//     localCache.data[key] = JSON.stringify(value);
// }

// function get_localcache(key) {
//     // key = CryptoJS.MD5(key).toString();
//     try {
//             return $.parseJSON(localCache.data[key]);
//         } catch (e) {
//             return null;
//         }
// }

// function remove_localcache(key) {
//     // key = CryptoJS.MD5(key).toString();
//     try {
//         delete localCache.data[key];
//     } catch (e) {}
// }

// function clear_localcache() {
//     localStorage.clear();
// }


function set_localcache(key, value) {
    value = JSON.stringify(value);
    try {
        localStorage.setItem(key, value);
    } catch (e) {
        console.log(e);
        localStorage.removeItem(key);
    }
}

function get_localcache(key) {
    return $.parseJSON(localStorage.getItem(key));
}

function remove_localcache(key) {
    localStorage.removeItem(key);
}

function clear_localcache() {
    console.log("LocalStorage cleared.");
    localStorage.clear();
}

// // function cache_remove(param) {
// //     param = CryptoJS.MD5(param).toString();
// //     delete localCache.data[param];
// // }

// function cache_exist(param) {
//     param = CryptoJS.MD5(param).toString();
//     return localCache.data.hasOwnProperty(param);
//     // if () {
//     //     return false
//     // } else {
//     //     return true
//     // }
// }

// function cache_get(param) {
//     param = CryptoJS.MD5(param).toString();
//     return localCache.data[param];
// }


// function cache_set(param, cachedData, callback) {
//     param = CryptoJS.MD5(param).toString();
//     // localCache.remove(param);
//     localCache.data[param] = cachedData;
//     if ($.isFunction(callback)) callback(cachedData);
// }