const app = require('express')();
const cors = require('cors');
// const corsOptions ={
//     origin:'http://127.0.0.1:8000',
//     credentials:true,            //access-control-allow-credentials:true
//     optionSuccessStatus:200
// }

app.use(cors());
const http = require('http').Server(app);
const io = require('socket.io')(http,{
     cors: {
    origin: "http://127.0.0.1:8000",
    methods: ["GET", "POST"]
  }
});
var users = [];
http.listen(8005, function () {
    console.log('we are listening to port 8005');
});
const Redis = require("ioredis");
const redis = new Redis();
    redis.subscribe('private-channel', function() {
    console.log('subscribed to private-channel');
    });
redis.on('message', function   (channel,message) {
    if (channel == 'private-channel') {
        // console.log(message);
        let data =  JSON.parse(message).data.data;
        let receiver_id = data.receiver_id;
       console.log(data);
        let event = JSON.parse(message).event;
        console.log(data.content);
        // console.log(event);
        io.to(`${users[receiver_id]}`).emit(channel + ':' + event, data);
    }


});
io.on('connection', function (socket) {
    socket.on("user_connected", function (user_id) {
        users[user_id] = socket.id;
        io.emit('updateUserStatus', users);
        console.log("user connected "+ user_id);
    });
    socket.on('disconnect', function () {
    var i  = users.indexOf(socket.id);
        users.splice(i, 1, 0);
        io.emit('updateUserStatus', users);
        console.log('user id'+i+' is disconnected and remaining users is '+ users);
    });
});

