angular.module('HttpGet',[]).factory('getData',function($http,$q){
    return function(method,url,data){
        var defer = $q.defer();
        $http({
            method:method,
            url:url,
            data: data,
            headers: {
               /* 'Authorization':'code_bunny',*/
                'Content-Type':'application/x-www-form-urlencoded'
            }
            // transformRequest:function(data){
            //     console.log(data === jsonData);
            //     return data
            // },
           /* transformResponse:function(data){
                return $.serialize(data)['name']
            }*/
            //cache: boolean or Cache object,
            //timeout: 1000,
            //withCredientials: boolean
        }).success(function(data,status,headers,config){
            defer.resolve(data);
        }).error(function(data,status,headers,config){
            defer.reject(data)
        });
        return defer.promise
    }
});