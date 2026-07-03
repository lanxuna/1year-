<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>一周年快乐，uno！</title>
    <style>
        :root {
            --splat-yellow: #d1e200;
            --splat-purple: #5c00e6;
            --text-color: #b2fff6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: #111;
            color: var(--text-color);
            overflow-x: hidden;
        }

        /* --- 动态颜料流动 Canvas 背景 --- */
        #inkCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -2;
            pointer-events: none;
        }

        .vignette-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: radial-gradient(circle, transparent 30%, #6408ff73 100%);
            z-index: -1;
            pointer-events: none;
        }

        /* 漂浮粒子样式 */
        .particle {
            position: fixed;
            bottom: -50px;
            font-size: 32px;
            pointer-events: none;
            z-index: 9999;
            user-select: none;
            opacity: 0;
            animation: floatUpAnimation 7s linear forwards;
        }

        @keyframes floatUpAnimation {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.8;
            }

            90% {
                opacity: 0.8;
            }

            100% {
                transform: translateY(-115vh) translateX(60px) rotate(360deg);
                opacity: 0;
            }
        }

        /* ================= 核心修复：锁定画册最大高度，彻底防止遮挡文字 ================= */
        .slider-container {
            width: 100%;
            max-width: 600px;
            height: 400px; 
            margin: 0 auto;
            position: relative;
            border-bottom: 4px dashed var(--splat-yellow);
            background: #6704fb28;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border-radius: 16px 16px 0 0;
            z-index: 2;
            box-shadow: 0 10px 20px #e2f30a62;
            overflow: hidden;
        }

        @media (max-width: 600px) {
            .slider-container {
                height: 300px;
            }
        }

        .slider {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .slider img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            opacity: 0;
            transition: opacity 0.5s ease-in-out; /* 稍微加长渐变时间让过渡更柔和 */
        }

        .slider img.active {
            opacity: 1;
        }

        /* --- 信件主体内容 --- */
        .letter-content {
            position: relative;
            z-index: 1;
            padding: 30px 20px;
            max-width: 600px;
            margin: 20px auto;
            line-height: 1.9;
            font-size: 16px;
            letter-spacing: 1px;
            background: rgb(17, 59, 56, 0.54);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 16px;
            border: 2px solid rgb(46, 236, 248, 0.10);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .salutation {
            font-size: 20px;
            font-weight: bold;
            color: var(--splat-yellow);
            margin-bottom: 25px;
            border-left: 4px solid var(--splat-purple);
            padding-left: 10px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }

        .paragraph {
            margin-bottom: 22px;
            text-align: justify;
            opacity: 0;
            transform: translateY(20px);
            transition: all 1s ease;
            text-shadow: 1px 1px 3px #111;
        }

        .paragraph.show {
            opacity: 1;
            transform: translateY(0);
        }

        .highlight-yellow {
            color: var(--splat-yellow);
            font-weight: bold;
        }

        .highlight-purple {
            color: #b78cff;
            font-weight: bold;
        }

        .signature {
            text-align: right;
            margin-top: 45px;
            font-size: 14px;
            color: #ccc;
        }

        /* --- 底部彩蛋按钮 --- */
        .nice-btn-container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 30px 0 60px 0;
        }

        .nice-btn {
            background: var(--splat-purple);
            color: var(--splat-yellow);
            border: 3px solid var(--splat-yellow);
            padding: 12px 35px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(92,0,230,0.6);
            transition: transform 0.1s;
            -webkit-tap-highlight-color: transparent;
        }

        .nice-btn:active {
            transform: scale(0.95);
        }

        .nice-pop {
            position: fixed;
            color: var(--splat-yellow);
            font-weight: bold;
            font-size: 24px;
            z-index: 99999;
            pointer-events: none;
            text-shadow: 2px 2px 0px #000;
            animation: popOut 0.8s ease-out forwards;
        }

        @keyframes popOut {
            0% {
                transform: scale(0.5) translate(0, 0);
                opacity: 1;
            }
            100% {
                transform: scale(1.6) translate(var(--mx), var(--my));
                opacity: 0;
            }
        }
    </style>
</head>
<body>

    <canvas id="inkCanvas"></canvas>
    <div class="vignette-overlay"></div>

<div class="slider-container">
    <div class="slider" id="imageSlider">
        <?php
            // 确保在这个 PHP 文件同级目录下有一个名为 images 的文件夹
            $dir = "images/"; 
            
            if (is_dir($dir)) {
                // 扫描文件夹内所有文件
                $files = scandir($dir);
                $index = 0;
                
                foreach ($files as $file) {
                    // 获取文件后缀并统一转换为小写，提升兼容性
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    
                    // 只挑选常见的图片格式，过滤掉隐藏文件等
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $activeClass = ($index === 0) ? 'class="active"' : '';
                        echo '<img src="' . $dir . $file . '" alt="回忆' . ($index + 1) . '" ' . $activeClass . '>' . "\n";
                        $index++;
                    }
                }
                
                // 如果没扫到任何图片，输出一句提示
                if ($index === 0) {
                    echo '<p style="color:var(--splat-yellow); text-align:center; padding-top:150px;">这里还没上传照片喔~</p>';
                }
            } else {
                echo '<p style="color:var(--splat-yellow); text-align:center; padding-top:150px;">未找到 images 文件夹</p>';
            }
        ?>
    </div>
</div>

    <div class="letter-content">
        <div class="salutation">给全世界最棒的盼盼/uno：</div>

        <div class="paragraph">好快啊，居然认识一年了。</div>

        <div class="paragraph">缘分真的很奇妙，深夜，匹配大厅里，互动起来的墨汁，一次偶遇，两个耐心的海产，居然能让地球自转<span class="highlight-yellow">365天</span>！</div>

        <div class="paragraph">不像天天下雨的这里，蛮颓镇的阳光很是灿烂，偶们在热闹的梦幻的祭典夜晚抛出了四季的烟花弹，私房里找角度拍照、用竹狙戳字的是哪只长直又是哪只锅盖？新年的烟花绽放我们又在动森度过美妙的夜晚......枪林弹雨鱿飞鲑打的记忆也实在是深刻🥀狡猾的热腾腾居然敢加速我几百乃至一千个小时！</div>

        <div class="paragraph">暑假结束之后，大家都开始忙碌，上线的时间变少了，但幸运的是，我们没有走散。我们好像没有聊过什么宏大的人生命题，但是却为奶蛙创造了一个世界。想要多多看到eno和umi的故事和萌照！</div>

        <div class="paragraph">uno是1，是第一，是唯一，谢谢你在一年前的深夜回应了我的蛄蛹！成为喷鱿一周年快乐！噢噢对了你的生日也快了，提前祝你生日快乐鱿～希望在你新的一岁与更远的未来，不管是现生还是游戏都顺顺利，也希望不管我们多久没上线，只要发一只奶蛙，就能回到那个悠然快乐的夏天。</div>

        <div class="signature">
            <p>爱改游戏名的ovo</p>
            <p>2026年 夏</p>
        </div>
    </div>

    <div class="nice-btn-container">
        <button class="nice-btn" onclick="fireNice(event)">还记得发射过多少赞气弹吗？</button>
    </div>

    <script>
        // --- 颜料流体动画（手机端高清完美斑点适配版） ---
        const canvas = document.getElementById('inkCanvas');
        const ctx = canvas.getContext('2d');

        let width, height;
        let dpr = window.devicePixelRatio || 1;

        let step = window.innerWidth < 600 ? 2 : 4;
        let scaleRate = window.innerWidth < 600 ? 0.012 : 0.005;

        function resize() {
            width = window.innerWidth;
            height = window.innerHeight;

            canvas.width = width * dpr;
            canvas.height = height * dpr;
            canvas.style.width = width + 'px';
            canvas.style.height = height + 'px';

            ctx.scale(dpr, dpr);
        }

        window.addEventListener('resize', resize);
        resize();

        let time = 0;

        function drawInkFlow() {
            time += 0.004;
            ctx.fillStyle = '#9dfaf2'; 
            ctx.fillRect(0, 0, width, height);
            ctx.fillStyle = '#ffbfdf'; 

            for (let y = 0; y < height; y += step) {
                for (let x = 0; x < width; x += step) {
                    let nx = x * scaleRate;
                    let ny = y * scaleRate;
                    let val = Math.sin(nx + time) +
                        Math.sin(ny + time * 0.8) +
                        Math.sin((nx + ny + time) * 0.5) +
                        Math.cos(Math.sqrt(nx * nx + ny * ny) - time);
                    if (val > 0.3) {
                        ctx.fillRect(x, y, step, step);
                    }
                }
            }
            requestAnimationFrame(drawInkFlow);
        }
        drawInkFlow();

        // --- 纯净淡入淡出轮播 ---
        const imgs = document.querySelectorAll('#imageSlider img');
        let currentImgIndex = 0;

        // 增加了判空保护，只有图片数量大于1时才启动轮播
        if (imgs.length > 1) {
            setInterval(() => {
                imgs[currentImgIndex].classList.remove('active');
                currentImgIndex = (currentImgIndex + 1) % imgs.length;
                imgs[currentImgIndex].classList.add('active');
            }, 2500); // 建议放慢到 2500 毫秒（2.5秒），方便慢慢欣赏
        }

        // --- 滚动文字逐渐浮现效果 ---
        const paragraphs = document.querySelectorAll('.paragraph');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                }
            });
        }, { threshold: 0.1 });

        paragraphs.forEach(p => observer.observe(p));

        // --- 粒子漂浮 ---
        const pool = ['✨', '🐙', '🦑'];

        function createFloatingParticle() {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            particle.innerText = pool[Math.floor(Math.random() * pool.length)];
            particle.style.left = (Math.random() * 95) + 'vw';
            const duration = (Math.random() * 4 + 5);
            particle.style.animationDuration = duration + 's';
            document.body.appendChild(particle);
            setTimeout(() => { particle.remove(); }, duration * 1000);
        }

        for (let i = 0; i < 4; i++) {
            setTimeout(createFloatingParticle, i * 600);
        }
        setInterval(createFloatingParticle, 500);

        // --- 点击发射 Nicedan ---
        function fireNice(e) {
            for (let i = 0; i < 6; i++) {
                const pop = document.createElement('div');
                pop.classList.add('nice-pop');
                pop.innerText = 'Nice';
                const mx = (Math.random() - 0.5) * 300 + 'px';
                const my = (Math.random() - 0.8) * 300 + 'px';
                pop.style.setProperty('--mx', mx);
                pop.style.setProperty('--my', my);
                pop.style.left = e.clientX + 'px';
                pop.style.top = e.clientY + 'px';
                document.body.appendChild(pop);
                setTimeout(() => { pop.remove(); }, 800);
            }
        }
    </script>
</body>
</html>