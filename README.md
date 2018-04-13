
# Omega-Server

[https://github.com/TeamLazaro/Omega](https://github.com/TeamLazaro/Omega)

# todo
- [ ] Have the login page on the Omega server.
- [x] Deploy needs to account for log and db files and set permissions
- [ ] Disable the interface and show appropriate feedback when computations or fetchings are being performed
- [ ] Check if expired cookies are sent to the server
- [ ] Sort the scheduler not "stopped" bug
- Add rupee symbol to the pricing sheet
- Convert shortcodes to human readable descriptions
- Notify us of an error in the log

# setup
Install these PHP extensions,
apt-get install php7.0-mbstring
apt-get install php7.0-gd
apt-get install php7.0-xml
apt-get install php7.0-zip
apt-get install php7.0-curl

pm2 start index.js --name="enquiry_processor" -i 1 --wait-ready --kill-timeout 15000
