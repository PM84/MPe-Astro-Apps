// function set_localcache(key, value) {
//     // key = CryptoJS.MD5(key).toString();
//     remove_localcache(key);
//     localCache.data[key] = JSON.stringify(value);
// }

// function get_localcache(key) {
//     // key = CryptoJS.MD5(key).toString();
//     try {
//         console.log(localCache.data);
//             return $.parseJSON(localCache.data[key]);
//         } catch (e) {
//             console.log("konnte nicht gelesen werden");
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
//     // console.log("exist?");
//     console.log(localCache.data);
//     console.log(localCache.data["85a0a235593c48f57033fd5ab951f286"]);
//     // console.log(localCache.data[param])
//     console.log(param);
//     return localCache.data.hasOwnProperty(param);
//     // if () {
//     //     console.log('EXISTIERT NICHT');
//     //     return false
//     // } else {
//     //     console.log('EXISTIERT');
//     //     return true
//     // }
// }

// function cache_get(param) {
//     param = CryptoJS.MD5(param).toString();
//     console.log('Getting from cache for param' + param);
//     return localCache.data[param];
// }


// function cache_set(param, cachedData, callback) {
//     param = CryptoJS.MD5(param).toString();
//     // localCache.remove(param);
//     localCache.data[param] = cachedData;
//     if ($.isFunction(callback)) callback(cachedData);
// }