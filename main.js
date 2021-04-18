let playpause_btn = document.querySelector(".playpause-track");
let seek_slider = document.querySelector(".seek_slider");
//let volume_slider = document.querySelector(".volume_slider");
let curr_time = document.querySelector(".current-time");
let total_duration = document.querySelector(".total-duration");
let rewind_btn = document.querySelector(".rewind_btn");
let fastFwd_btn = document.querySelector(".fastFwd_btn");
let playback_speed = document.querySelector(".playback-speed");


let track_index = 0;
let isPlaying = false;
let updateTimer;
let playBackRateOpts = [
  0.25,
  0.5,
  0.75,
  1,
  1.25,
  1.5,
  1.75,
  2,
  2.25,
  2.5,
  2.75,
  3
];
let playbackRateCounter = 3;


//get the GET variables and values into array
let url = window.location.href;
let vars = url.split("?")[1].split("&");
let GETvals = {};
for(element of vars){
  GETvals[element.split("=")[0]]=element.split("=")[1];
}
console.log(GETvals);

//read text file with timestamps and word associated with them
//only way to read is with get request and url therefore:
/*
function getText(){
  // read text from URL location
  var request = new XMLHttpRequest();
  let url = "http://2.234.129.34/Recording/".concat(GETvals['folderName'],"/timestamp.txt");
  request.open('GET', url, true);
  request.send(null);
  request.onreadystatechange = function () {
      if (request.readyState === 4 && request.status === 200) {
          var type = request.getResponseHeader('Content-Type');
          if (type.indexOf("text") !== 1) {
              return request.responseText;
          }
      }
  }
}*/
// doesn't work, should add header to grant permissions
//problem solved by passing values with php trough a script tag
let timestampsHTML = document.getElementById("timestamps").innerHTML;
let data = timestampsHTML.split("#");
let timestamp = [];
for(i=0;i<data.length;i++){
  let temp = data[i].split("_");
  if(!isNaN(parseFloat(temp[0]))){
    timestamp[i]=temp;
    timestamp[i][0] = parseFloat(timestamp[i][0]);
    timestamp[i][1] = parseFloat(timestamp[i][1]);
  }
}

console.log(timestamp);

//add event listeners for all words 
/*
for (counter of Object.keys(timestamp)){
  document.getElementById("ts".concat(counter)).addEventListener("click",seekToWord(counter));
}*/


// Create new audio element
let curr_track = document.createElement('audio');
//path is passed by php to var audioRecordingPath
let trackPath = audioRecordingPath;
console.log(trackPath);
let pauseIcon = document.getElementById("pauseBtn").innerHTML;
let playIcon = document.getElementById("playBtn").innerHTML;
let rewindBtnIcon = document.getElementById("rewindBtnIcon").innerHTML;
let fastFwdBtnIcon = document.getElementById("fastFwdBtnIcon").innerHTML;
//set internal icon of playpause_btn
playpause_btn.innerHTML=playIcon;
rewind_btn.innerHTML=rewindBtnIcon;
fastFwd_btn.innerHTML=fastFwdBtnIcon;




function loadTrack() {
  clearInterval(updateTimer);
  resetValues();
  curr_track.src = trackPath;
  curr_track.load();

  updateTimer = setInterval(seekUpdate, 100);
}

//word highliting code
// gets arrays of words to be highlighted
function highlightText(idHighlits) {
  for (counter of Object.keys(timestamp)){
    let element= document.getElementById("ts".concat(counter));
    if(!(element === null)){
      element.classList.remove("highlight");
    }
  }
  for (textId of idHighlits){
    document.getElementById("ts".concat(textId)).classList.add("highlight");
  }
}
//gets id of word to be highlighted
function findWord(currentTime){
  let ids = [];
  for (counter of Object.keys(timestamp)){
    if((timestamp[counter][0] <= currentTime)&&(timestamp[counter][1] > currentTime)){
      //console.log(timestamp[currentWordId][0]);
      ids.push(counter);
    }
  }
  highlightText(ids);
}

//updating time display and slider position
function updateSlider(){
  let seekPosition = 0;
  
  seekPosition = curr_track.currentTime * (100 / curr_track.duration);

  seek_slider.value = seekPosition;

  let currentMinutes = Math.floor(curr_track.currentTime / 60);
  let currentSeconds = Math.floor(curr_track.currentTime - currentMinutes * 60);
  let durationMinutes = Math.floor(curr_track.duration / 60);
  let durationSeconds = Math.floor(curr_track.duration - durationMinutes * 60);

  if (currentSeconds < 10) { currentSeconds = "0" + currentSeconds; }
  if (durationSeconds < 10) { durationSeconds = "0" + durationSeconds; }
  if (currentMinutes < 10) { currentMinutes = "0" + currentMinutes; }
  if (durationMinutes < 10) { durationMinutes = "0" + durationMinutes; }

  curr_time.textContent = currentMinutes + ":" + currentSeconds;
  total_duration.textContent = durationMinutes + ":" + durationSeconds;
}



function resetValues() {
  curr_time.textContent = "00:00";
  total_duration.textContent = "00:00";
  seek_slider.value = 0;
}

// Load the first track in the tracklist
loadTrack();

function playpauseTrack() {
  if (!isPlaying) playTrack();
  else pauseTrack();
}

function playTrack() {
  curr_track.play();
  isPlaying = true;
  playpause_btn.innerHTML = pauseIcon;
}

function pauseTrack() {
  curr_track.pause();
  isPlaying = false;
  playpause_btn.innerHTML = playIcon;
}

function slowTrack() {
  if ((playbackRateCounter-1) >=  0){
    playbackRateCounter -= 1;
  }
  curr_track.playbackRate = playBackRateOpts[playbackRateCounter];
  if (playBackRateOpts[playbackRateCounter] != 1){
    playback_speed.innerHTML = playBackRateOpts[playbackRateCounter];
  } else {
    playback_speed.innerHTML = "";
  }
}

function fastFwdTrack() {
  if ((playbackRateCounter+1) <  playBackRateOpts.length){
    playbackRateCounter += 1;
  }
  curr_track.playbackRate = playBackRateOpts[playbackRateCounter];
  if (playBackRateOpts[playbackRateCounter] != 1){
    playback_speed.innerHTML = playBackRateOpts[playbackRateCounter];
  } else {
    playback_speed.innerHTML = "";
  }
}

function seekTo() {
  seekto = curr_track.duration * (seek_slider.value / 100);
  curr_track.currentTime = seekto;
  updateSlider();
  findWord(curr_track.currentTime);
  prevTime=seekto+2;
}

/*
//function to click word and move timestamp, doesn't work
function seekToWord(clicked_id){
  console.log(clicked_id);
  curr_track.currentTime = timestamp[clicked_id][0];
}*/

/*
function setVolume() {
  curr_track.volume = volume_slider.value / 100;
}*/

var prevTime=0;
function seekUpdate() {

  if (!isNaN(curr_track.duration)) {
    //find word that matches current time
    if(isPlaying){
      findWord(curr_track.currentTime);
    }

    if((curr_track.currentTime-prevTime)>=1){
      updateSlider();
      prevTime = curr_track.currentTime;
    }    
  }
}

