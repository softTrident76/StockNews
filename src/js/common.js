var objectUrl;
var duration = 0;

//function to load url for deleting respective items from database
function delete_item(e) {
    var id = e.target.getAttribute('data-id');
    var type = e.target.getAttribute('data-type');
    var msg = '';
    var url = '';

    switch (type) {
        case 'admin':
            var msg = 'You want to delete this Admin User';
            url = '/index.php/adminuser/delete/' + id;
            break;
        case 'media':
            var msg = 'You want to delete this Media File';
            url = baseURL + 'deleteMedia/' + id;
            break;
        case 'radio':
            var msg = 'You want to delete this Radio Channel';
            url = '/index.php/radiochannel/delete/' + id;
            break;
        case 'video':
            var msg = 'You want to delete this Video File';
            url = baseURL + 'deleteVideo/' + id;
            break;
        case 'reports':
            var msg = 'You want to delete this reported comment';
            url = baseURL + 'deleteReport/' + id;
            break;
        case 'artist':
            var msg = 'You want to delete this Artist';
            url = baseURL + 'deleteArtist/' + id;
            break;
        case 'album':
            var msg = 'You want to delete this Album';
            url = baseURL + 'deleteAlbum/' + id;
            break;
        case 'genre':
            var msg = 'You want to delete this Genre';
            url = baseURL + 'deleteGenre/' + id;
            break;

    }
    console.log(url);
    swal({
        title: 'Are you sure?',
        text: msg,
        type: 'warning',
        confirmButtonColor: "#DD6B55",
        showCancelButton: true,
        confirmButtonText: 'Sure'
    }, function() {
        document.location.href = url;
    });
}


function user_action(e) {
    var id = e.target.getAttribute('data-id');
    var action = e.target.getAttribute('data-action');
    var blocked = e.target.getAttribute('data-blocked');
    console.log(action + ", " + blocked);
    var msg = '';
    var url = '';

    switch (action) {
        case 'block':
            if (blocked == 1) {
                msg = 'You want to block this User';
                url = '/index.php/androidUser/unblock/' + id;
                console.log(url);
            } else {
                msg = 'You want to unblock this User';
                url = '/index.php/androidUser/block/' + id;
                console.log(url);
            }

            break;
        case 'delete':
            msg = 'You want to delete this User';
            url = '/index.php/androidUser/delete/' + id;
            break;
    }
    swal({
        title: 'Are you sure?',
        text: msg,
        type: 'warning',
        confirmButtonColor: "#DD6B55",
        showCancelButton: true,
        confirmButtonText: 'Sure'
    }, function() {
        document.location.href = url;
    });
}

function comment_action(e) {
    var id = e.target.getAttribute('data-id');
    var action = e.target.getAttribute('data-action');
    var deleted = e.target.getAttribute('data-deleted');
    console.log(action);
    var msg = '';
    var url = '';

    switch (action) {
        case 'publish':
            if (deleted == 1) {
                msg = 'You want to unpublish this comment, users wont be able to see this comment if you unpublish.';
                url = '/index.php/comment/publish/' + id;
            } else {
                msg = 'You want to publish this comment, users will be able to see this comment if you publish.';
                url = '/index.php/comment/unpublish/' + id;
            }

            break;
        case 'delete':
            msg = 'You want to completely thrash this comment and all corresponding replies, this action cannot be undone.';
            url = '/index.php/comment/delete/' + id;
            break;
    }
    swal({
        title: 'Are you sure?',
        text: msg,
        type: 'warning',
        confirmButtonColor: "#DD6B55",
        showCancelButton: true,
        confirmButtonText: 'Sure'
    }, function() {
        document.location.href = url;
    });
}

$('.thumbs_dropify').dropify({
    messages: {
        'default': 'Drag or drop thumbnail here',
        'replace': 'Drag and drop or click to replace',
        'remove': 'Remove',
        'error': 'Ooops, something wrong happended.'
    }
});

//initialise dropify for song upload
$('.dropify').dropify({
    messages: {
        'default': 'Drag or drop mp3 here',
        'replace': 'Drag and drop or click to replace',
        'remove': 'Remove',
        'error': 'Select only mp3 files.'
    }
});

//initialise dropify for image upload
$('.dropify2').dropify({
    messages: {
        'default': 'Drag or drop cover photo here',
        'replace': 'Drag and drop or click to replace',
        'remove': 'Remove',
        'error': 'Select only jpeg|jpg|png|JPEG|PNG image files.'
    }
});

//initialise dropify for video upload
$('.dropify3').dropify({
    messages: {
        'default': 'Drag or drop mp4 here',
        'replace': 'Drag and drop or click to replace',
        'remove': 'Remove',
        'error': 'Select only mp4 files.'
    }
});

$('#categories-table').DataTable({
    "pageLength": 20,
    dom: 'frtip'
});

$('#reports_table').DataTable({
    "pageLength": 20,
    dom: 'frtip'
});


$('#admin_table').DataTable({
    "pageLength": 20,
    dom: 'frtip'
});

$('#users_table').DataTable({
    "bProcessing": true,
    "serverSide": true,
    "pageLength": 20,
    "ajax": {
        url: baseURL + "getUsersAjax",
        type: 'POST'
    },
    dom: 'frtip'
});

//initialise audios data table
//items are fetched through ajax
$('#media_table').DataTable({
    "bProcessing": true,
    "serverSide": true,
    "pageLength": 10,
    "ajax": {
        url: baseURL + "fetchMedias",
        type: 'POST'
    },
    dom: 'frtip',
    "columnDefs": [
        { className: "td_width", "targets": [0, 1, 2, 3] }
    ]
});

function error_alert(msg) {
    swal({
        title: 'Error!',
        text: msg,
        type: 'warning',
        confirmButtonClass: 'btn btn-success'
    });
}

function success_alert(msg) {
    swal({
        title: 'Success!',
        text: msg,
        type: 'success',
        confirmButtonClass: 'btn btn-success'
    });
}

function isValidURL(string) {
    var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
    if (res == null)
        return false;
    else
        return true;
};


//upload new audio file
function uploadNewAudio(event) {
    event.preventDefault();
    var artist = $('#artist').selectpicker('val');
    var album = $('#album').selectpicker('val');
    var genre = $('#genre').selectpicker('val');
    var title = $("#title").val();
    var media_type = $('#media_type').selectpicker('val');
    var lyrics = $("#lyrics").val();
    var is_free = $('#is_free').selectpicker('val');
    var can_download = $('#can_download').selectpicker('val');
    var can_preview = $('#can_preview').selectpicker('val');
    var _prv_duration = $("#preview_duration").val();
    var preview_duration = _prv_duration == "" ? 0 : _prv_duration;
    var notify = $('#notify').selectpicker('val');

    var thumbnail_link = $("#thumbnail_link").val();
    var media_link = $("#media_link").val();

    if (title == "") {
        error_alert('Please add title for the media file');
        return;
    }

    if (media_type == 1 && !isValidURL(thumbnail_link)) {
        error_alert('Please provide a valid thumbnail link for the media file.');
        return;
    }

    if (media_type == 1 && !isValidURL(media_link)) {
        error_alert('provide a valid media link to an audio file.');
        return;
    }

    var thumbnail = document.getElementById('thumbnail');
    var _thumbnail = thumbnail.files[0];
    if (_thumbnail == undefined && media_type == 0) {
        error_alert('Please select a cover photo.');
        return;
    }

    var audio = document.getElementById('media-file');
    var _audio = audio.files[0];
    if (_audio == undefined && media_type == 0) {
        error_alert('Please select a media file or provide a media link.');
        return;
    }

    show_loader();
    var _form_object = {
        artists: artist,
        album: album,
        genre: genre,
        media_type: media_type,
        thumbnail_link: thumbnail_link,
        media_link: media_link,
        title: title.trim(),
        lyrics: lyrics,
        is_free: is_free,
        can_download: can_download,
        can_preview: can_preview,
        preview_duration: preview_duration,
        duration: duration,
        notify: notify
    };
    //console.log(form_obj); return;
    if (media_type == 0) {
        _form_object.duration = duration;
        var form_obj = JSON.stringify(_form_object);
        //console.log(form_obj); return;
        var fd = new FormData();
        fd.append("data", form_obj);
        fd.append("thumbnail", _thumbnail);
        fd.append("audio", _audio);
        ajax_push(fd);
    } else {
        preloadAudio(media_link, _form_object);
    }

}

function preloadAudio(url, _form_object) {
    console.log("trying to preload " + url);
    var audio = new Audio();
    // once this file loads, it will call loadedAudio()
    // the file will be kept by the browser as cache
    audio.addEventListener('canplaythrough', function(e) {
        var seconds = e.currentTarget.duration;
        duration = (seconds * 1000) | 0;
        console.log(duration);
        _form_object.duration = duration;
        var form_obj = JSON.stringify(_form_object);
        //console.log(form_obj); return;
        var fd = new FormData();
        fd.append("data", form_obj);
        ajax_push(fd);
    }, false);

    audio.addEventListener('error', function failed(e) {
        error_alert("COULD NOT LOAD AUDIO, please provide a valid audio link");
        hide_loader();
    });
    audio.src = url;
    audio.load(); // add this line
}

function ajax_push(fd) {
    error_alert("This is just a test server");
}

//update uploaded audio file
function updateAudio(event) {
    event.preventDefault();
    var artist = $('#artist').selectpicker('val');
    var album = $('#album').selectpicker('val');
    var genre = $('#genre').selectpicker('val');
    var title = $("#title").val();
    var media_type = $('#media_type').selectpicker('val');
    var lyrics = $("#lyrics").val();
    var is_free = $('#is_free').selectpicker('val');
    var can_download = $('#can_download').selectpicker('val');
    var can_preview = $('#can_preview').selectpicker('val');
    var _prv_duration = $("#preview_duration").val();
    var preview_duration = _prv_duration == "" ? 0 : _prv_duration;
    var id = $("#id").val();

    if (title == "") {
        error_alert('Please add title for the media file');
        return;
    }

    show_loader();
    var form_obj = JSON.stringify({
        id: id,
        artists: artist,
        album: album,
        genre: genre,
        title: title.trim(),
        lyrics: lyrics,
        is_free: is_free,
        can_download: can_download,
        can_preview: can_preview,
        preview_duration: preview_duration
    });
    //console.log(form_obj); return;
    var fd = new FormData();
    fd.append("data", form_obj);

    makeAjaxCall(baseURL + "", "POST", fd).then(function(data) {
        hide_loader();
        console.log("render user details", data);
        if (data.status == "ok") {
            success_alert(data.msg);
        } else {
            error_alert(data.msg);
        }
    }, function(status) {
        hide_loader();
        console.log("failed with status" + status);
        error_alert("failed with status " + status);
    });

}


function processResponse(data) {
    hide_loader();
    console.log("render user details", data);
    if (data.status == "ok") {
        success_alert(data.msg);
        //clear form elements
        var form = document.getElementById('upload-form');
        form.elements[0].value = "";
        form.elements[1].value = "";

        //clear dropify elements
        $('.dropify-clear').click();
        $('.dropify2-clear').click();
        $('.dropify3-clear').click();

    } else {
        error_alert(data.msg);
    }
}

function show_loader() {
    var submit = document.getElementById('submit');
    var loader = document.getElementById('loader');
    if (submit != undefined) {
        submit.style.display = 'none';
    }
    if (loader != undefined) {
        loader.style.display = 'block';
    }
}

function hide_loader() {
    var submit = document.getElementById('submit');
    var loader = document.getElementById('loader');
    if (submit != undefined) {
        submit.style.display = 'block';
    }
    if (loader != undefined) {
        loader.style.display = 'none';
    }
}

//listener to check if an audio file was playing
//then pause it
document.addEventListener('play', function(e) {
    var audios = document.getElementsByTagName('audio');
    for (var i = 0, len = audios.length; i < len; i++) {
        if (audios[i] != e.target) {
            audios[i].pause();
        }
    }
}, true);

function warn_user(evt) {
    evt.preventDefault();
    var el = evt.target;
    var email = el.getAttribute('data-email');
    var comment = el.getAttribute('data-comment');

    swal({
            title: "",
            text: "Block Alert Warning",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            animation: "slide-from-top",
            inputPlaceholder: "Warn user of consquences of making such comments",
            showLoaderOnConfirm: true
        },
        function(message) {
            if (message === false) return false;

            if (message === "") {
                swal.showInputError("You need to write something!");
                return false;
            }

            show_loader();
            var form_obj = JSON.stringify({
                email: email,
                comment: comment,
                message: message
            });
            //console.log(form_obj); return;
            var fd = new FormData();
            fd.append("data", form_obj);

            makeAjaxCall(baseURL + "reportedCommentWarnEmail", "POST", fd).then(function(response) {
                if (data.status == "ok") {
                    success_alert(data.msg);
                } else {
                    error_alert(data.msg);
                }
            }, function(status) {
                console.log("failed with status", status);
                error_alert("failed with status " + status);
            });
        });
}

function view_comments_by_date() {
    var date = document.getElementById('reportrange').value;
    if (date != "") {
        var res = date.split(" - ");
        if (res[0] == undefined || res[1] == undefined) {
            error_alert("Selected date(s) is invalid!!");
            return;
        }
        var date1 = res[0];
        var date2 = res[1];
        load_comments(date1, date2);

    }
}

function load_comments(date1, date2) {
    $('#comments_table').DataTable({
        "bDestroy": true,
        "bProcessing": true,
        "serverSide": true,
        "pageLength": 20,
        "ajax": {
            url: baseURL + "getCommentsAjax?date=" + date1 + "&date2=" + date2,
            type: 'POST'
        },
        dom: 'frtip'
    });
}
load_comments(0, 0);

$("#media_type").change(function() {
    var type = $('#media_type option:selected').val();
    var upload_div = document.getElementById('upload_div');
    var link_div = document.getElementById('link_div');
    if (type == 0) {
        upload_div.style.display = 'block';
        link_div.style.display = 'none';
    } else {
        upload_div.style.display = 'none';
        link_div.style.display = 'block';
    }
});



function get_duration(event) {
    duration = 0;
    var file = event.target.files[0];
    var file = event.currentTarget.files[0];
    objectUrl = URL.createObjectURL(file);
    $("#audio").prop("src", objectUrl);
}
//$("#audio").prop("src", "https://file-examples.com/wp-content/uploads/2017/11/file_example_WAV_2MG.wav");
$("#audio").on("canplaythrough", function(e) {
    var seconds = e.currentTarget.duration;
    duration = (seconds * 1000) | 0;
    console.log(duration);

    if (objectUrl != undefined) {
        URL.revokeObjectURL(objectUrl);
    }
});

function formatTime(seconds) {
    const h = Math.floor(seconds / 3600)
    const m = Math.floor((seconds % 3600) / 60)
    const s = seconds % 60
    return [h, m > 9 ? m : h ? '0' + m : m || '0', s > 9 ? s : '0' + s]
        .filter(a => a)
        .join(':');
}