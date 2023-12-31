<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<canvas id="myCanvas" width="1200" height="400"></canvas>
<button onclick="postSelectedTitles()">ارسال</button>
<div id="postedTitles"></div>
</body>
<script>
    // تعریف متغیرها
    var canvas = document.getElementById("myCanvas");
    var ctx = canvas.getContext("2d");
    var circles = [];
    var selectedCircles = [];

    // تابع برای ایجاد دایره
    function Circle(x, y, radius, color, title, dx, dy) {
        this.x = x;
        this.y = y;
        this.radius = radius;
        this.color = color;
        this.title = title;
        this.dx = dx;
        this.dy = dy;
    }

    // تابع برای رسم دایره
    Circle.prototype.draw = function() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.radius, 0, 2 * Math.PI, false);
        ctx.fillStyle = this.color;
        ctx.fill();
        ctx.lineWidth = 1;
        ctx.strokeStyle = "#000";
        ctx.stroke();
        ctx.font = "12px Arial";
        ctx.fillStyle = "#000";
        ctx.fillText(this.title, this.x - 20, this.y);
    };

    // تابع برای بروزرسانی دایره‌ها و حرکت آنها
    function update() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (var i = 0; i < circles.length; i++) {
            var circle = circles[i];

            // حرکت دایره
            circle.x += circle.dx;
            circle.y += circle.dy;

            // برخورد دایره با مرز صفحه
            if (circle.x + circle.radius > canvas.width || circle.x - circle.radius < 0) {
                circle.dx = -circle.dx;
            }
            if (circle.y + circle.radius > canvas.height || circle.y - circle.radius < 0) {
                circle.dy = -circle.dy;
            }

            circle.draw();
        }

        requestAnimationFrame(update);
    }

    // تابع برای بررسی کلیک کردن روی دایره
    function checkClick(event) {
        var mouseX = event.pageX - canvas.offsetLeft;
        var mouseY = event.pageY - canvas.offsetTop;

        for (var i = 0; i < circles.length; i++) {
            var circle = circles[i];
            var dx = mouseX - circle.x;
            var dy = mouseY - circle.y;
            var distance = Math.sqrt(dx * dx + dy * dy);

            if (distance < circle.radius) {
                // تغییر اندازه دایره
                circle.radius += 5;
                circle.draw();

                // اضافه کردن دایره به دایره‌های انتخاب شده
                selectedCircles.push(circle);

                break;
            }
        }
    }

    // تابع برای پست کردن عناوین دایره‌های انتخاب شده
    function postSelectedTitles() {
        var selectedTitles = [];
        for (var i = 0; i < selectedCircles.length; i++) {
            var circle = selectedCircles[i];
            selectedTitles.push(circle.title);
        }

        // ارسال عناوین دایره‌های انتخاب شده با Ajax
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "post_titles.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                // دریافت پاسخ و نمایش آن
                var response = xhr.responseText;
                document.getElementById("postedTitles").innerHTML = response;
            }
        };

        // تبدیل عناوین به پارامتر ارسالی
        var params = "titles=" + encodeURIComponent(selectedTitles.join(", "));

        // ارسال درخواست Ajax
        xhr.send(params);
    }

    // ایجاد 50 دایره با موقعیت و رنگ تصادفی
    for (var i = 0; i < 50; i++) {
        var x = Math.random() * (canvas.width - 40) + 20;
        var y = Math.random() * (canvas.height - 40) + 20;
        var radius = 20;
        var color = "#" + ((Math.random() * 0xffffff) << 0).toString(16);
        var title = "عنوان " + (i + 1);
        var dx = Math.random() * 2 - 1; // سرعت حرکت افقی
        var dy = Math.random() * 2 - 1; // سرعت حرکت عمودی
        circles.push(new Circle(x, y, radius, color, title, dx, dy));
    }

    // رسم دایره‌ها اولیه و شروع حرکت
    update();

    // بررسی کلیک کردن روی دایره
    canvas.addEventListener("click", checkClick);

</script>
</html>