<?php
// Enlightening All Launch Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Enlightening All 🌟</title>
    <style>
        body {
            background: #000000;
            color: #f5f5f5;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.7;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
        }

        /* --- Logos --- */
        .logo-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .logo-row .logo {
            max-width: 180px;
            height: auto;
        }

        @media (max-width: 500px) {
            .logo-row {
                flex-direction: column;
                gap: 20px;
            }
        }

        /* --- Text & Layout --- */
        h1 {
            font-size: clamp(1.8rem, 5vw, 2.8rem);
            color: #fff;
            margin-bottom: 15px;
            text-shadow: 0 0 15px #00e6ff, 0 0 25px #ff66cc;
        }
        h2 {
            font-size: clamp(1.4rem, 4vw, 2rem);
            color: #ffcc33;
            margin: 40px 0 20px;
            text-shadow: 0 0 10px #ff66cc;
        }
        h3 {
            font-size: clamp(1.2rem, 3.5vw, 1.5rem);
            color: #00e6ff;
            margin: 25px 0 10px;
        }
        p, li {
            font-size: 1.15rem;
            padding-left: 10px;
        }
        ul { list-style: none; padding: 0; }
        ul li::before { padding-left: 10px; color: #ff66cc; }
        .highlight-box {
            background: rgba(255, 102, 204, 0.08);
            border: 1px solid #ff66cc;
            border-radius: 12px;
            padding: 25px;
            margin: 30px auto;
            box-shadow: 0 0 20px rgba(255, 102, 204, 0.4);
            text-align: left;
        }

        /* --- Button --- */
        .cta {
            display: inline-block;
            margin-top: 25px;
            padding: 16px 44px;
            font-size: 1.2rem;
            color: #fff;
            background: linear-gradient(90deg, #00e6ff, #ff66cc);
            border-radius: 40px;
            text-decoration: none;
            text-transform: uppercase;
            font-weight: 700;
            box-shadow: 0 0 20px #00e6ff, 0 0 30px #ff66cc;
            transition: 0.3s ease;
        }
        .cta:hover { transform: scale(1.05); }

        /* --- Tooltip --- */
        .tooltip {
            position: relative;
            cursor: pointer;
            display: inline-block;
        }
        .tooltip .tooltip-content {
            visibility: hidden;
            opacity: 0;
            width: 90vw;
            max-width: 600px;
            background: #222;
            color: #fff;
            text-align: center;
            padding: 16px;
            border-radius: 8px;
            position: fixed;
            z-index: 1000;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            transition: opacity 0.3s;
            box-sizing: border-box;
            word-wrap: break-word;
            white-space: normal;
        }
        .tooltip.active .tooltip-content {
            visibility: visible;
            opacity: 1;
        }
        .tooltip .tooltip-content img {
            max-width: 100%;
            border-radius: 6px;
            height: auto;
        }
        .tooltip-close {
            display: block;
            text-align: right;
            font-size: 0.9rem;
            cursor: pointer;
            color: #ff66cc;
            margin-top: -8px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Logos at top -->
    <div class="logo-row">
        <img src="https://enlighteningall.com/images/layout/default-square.jpg" alt="Enlightening All Logo" class="logo">
        <img src="/images/web-design-fencl-logo.png" alt="Fencl Web Design Logo" class="logo">
    </div>

    <!-- Free Gift -->
    <div class="section highlight-box">
        <h2>🎁 Free Launch Gift</h2>
        <ul>
            <li>🎫 <b>1 Free Class for you + a friend</b></li>
            <li>🎫 <b>2 BFF BOGO Classes ($4 each)</b> — invite 2 friends at half price</li>
            <li>🎫 <b>Your friends receive the same offer</b> with a 90-Day Free Membership upgrade</li>
            <li>👉 <b>No payment required</b> to claim</li>
        </ul>
        💫 <b>Everyone starts with a Free 90-Day Membership Upgrade</b>
    </div>

    <!-- Membership -->
    <div class="section highlight-box">
        <h2>⭐ Membership Made Simple</h2>
        <ul>
            <li>👯 <b>Book together</b> – reserve side by side with friends</li>
            <li>⏰ <b>Early booking access</b> – reserve 2 days before free members</li>
            <li>💲 <b>Save $4 per class</b></li>
            <li>🌟 <b>Exclusive access</b> to entertainers, workshops, and special events</li>
            <li>💳 <b>10% off</b> food, drink, crystals, and more</li>
        </ul>
    </div>

    <!-- Classes -->
    <div class="section highlight-box">
        <h2>🧘 Classes & Experiences</h2>
        <h3>Yoga & Wellness:</h3>
        <ul>
            <li>
        <span class="tooltip">YogaBeachBody ⓘ
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            A dynamic flow designed to build strength, tone, and flexibility — yoga with a fitness edge.
          </span>
        </span>,
                <span class="tooltip">HipsBackNeckYoga ⓘ
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            Targeted practice to release tension in hips, back, and neck — ideal for posture and pain relief.
          </span>
        </span>,
                <span class="tooltip">Subconscious Reset ⓘ
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            A guided meditative yoga flow that combines breathwork and visualization to reset mental patterns.
          </span>
        </span>,
                <span class="tooltip">Chair Yoga ⓘ
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            Gentle yoga done seated or with chair support — accessible for all levels, especially beginners and seniors.
          </span>
        </span>
            </li>
            <li>
        <span class="tooltip">Kundalini ⓘ
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            A spiritual and energetic practice combining breath, movement, and chanting to awaken inner energy.
          </span>
        </span>,
                <span class="tooltip">Vinyasa ⓘ
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            Flow-based yoga linking breath with movement; builds strength, flexibility, and endurance.
          </span>
        </span>,
                <span class="tooltip">Hatha ⓘ
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            A foundational yoga style focusing on posture and breathing — slower-paced and great for all levels.
          </span>
        </span>,
                <span class="tooltip">Not Hot Yoga 26 & 2 ⓘ
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            A sequence of 26 poses and 2 breathing exercises (like Bikram) — but done without the heated room.
          </span>
        </span>
            </li>
            <li>
        <span class="tooltip">432 Hz Crystal Meditation + Yoga Nidra ⓘ
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            A deep relaxation practice using 432 Hz sound frequency for harmony and balance, paired with Yoga Nidra — guided yogic sleep for full mind-body restoration.
          </span>
        </span>
            </li>
            <li>Ecstatic Dance, Parent + Teen Nights</li>
        </ul>

        <h3>AI Education:</h3>
        <ul>
            <li>AI 101 – Everyday AI Made Simple</li>
            <li>Advanced AI Group Competitions</li>
        </ul>

        <h3>Mind-Body-Business:</h3>
        <ul>
            <li>MindBodyBusiness Bar Mixers</li>
            <li>MindBodyBusiness.net Networking</li>
            <li>Vendor Fairs + Wellness Providers Exchange</li>
        </ul>

        <h3>Music & Creative:</h3>
        <ul>
            <li>Tropical Influencers (Open Mic + Artist Showcase)</li>
            <li>Karaoke Night (
                <span class="tooltip">KaraFun Pro ℹ️
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            <img src="https://www.karafun.com/i/layout/karafun_karaoke_og.jpg" alt="KaraFun Pro">
            <br>Professional karaoke software (thousands of tracks, custom key & tempo, commercial licensing)
          </span>
        </span>
                )</li>
            <li>Original Music Nights (Songwriters’ Circle)</li>
        </ul>
    </div>

    <!-- Comfort -->
    <div class="section highlight-box">
        <h2>🪷 Comfort & Mats</h2>
        <ul>
            <li>All classes are 90 minutes</li>
            <li>
        <span class="tooltip">Premium oversized mats provided (limited availability) ℹ️
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            <img src="https://i5.walmartimages.com/seo/Yoga-Mat-TPE-Workout-Mat-Premium-6mm-Print-Extra-Thick-Non-Slip-Exercise-Fitness-Mat-Types-Yoga-Pilates-Floor-Workouts-72-L-x-24-W-x-6mm-Thick_4f519226-0b63-4ef5-acc2-e37995739614.22d8f572bda40a0b42a39a41b4e8ec7a.jpeg" alt="Premium Yoga Mat">
            <br>Example of the premium oversized yoga mat
          </span>
        </span>
            </li>
            <li>Bring your own towel/mat cover</li>
            <li>Rental towels $3 — sweat and leave it behind</li>
            <li>
                🎶 Music included with
                <span class="tooltip">BMI ℹ️
          <span class="tooltip-content"><span class="tooltip-close">✖</span>
            <img src="https://yt3.googleusercontent.com/oWgMWKEueZe-KKlUreTkumFlp-LC1NcYnaSPafo4ng5z85f1zSnpcjW2VJUnf9Xh7Cgligx89g=s900-c-k-c0x00ffffff-no-rj" alt="BMI Logo">
            <br>BMI (Broadcast Music, Inc.) – licensing to support songwriters and publishers when music is publicly performed.
          </span>
        </span>
                license
            </li>
        </ul>
    </div>

    <!-- Rewards -->
    <div class="section highlight-box">
        <h2>💲 Rewards & Credits</h2>
        <ul>
            <li>Earn $1/month for every friend you refer who stays a member</li>
            <li>Members earn credits toward classes, events, food & drink</li>
            <li>Team members and instructors share in community success with bonus rewards</li>
        </ul>
    </div>

    <!-- Why Join -->
    <div class="section highlight-box">
        <h2>⚡ Why Join</h2>
        <ul>
            <li>✅ Affordable: most classes $8–$12</li>
            <li>✅ Fair: no inflated pricing, no hidden markups</li>
            <li>✅ Rewarding: earn credits for loyalty & referrals</li>
            <li>✅ Community: yoga, wellness, AI, music, business — all in one place</li>
        </ul>
    </div>

    <!-- Act Now -->
    <div class="section highlight-box">
        <h2>🌟 Act Now</h2>
        <ul>
            <li>Classes limited to 30 spots — book early to save your space</li>
            <li>Free Launch Gift: 1 Free Class + 2 BFF BOGO Classes</li>
            <li>Invite your friends and enjoy your Free 90-Day Membership</li>
        </ul>
    </div>

    <a href="https://enlighteningall.com/events" class="cta">Join ENLIGHTENING ALL Today!</a>
</div>

<script>
    // Tooltip toggle for click/tap
    document.querySelectorAll('.tooltip').forEach(function(el) {
        el.addEventListener('click', function(e) {
            if (e.target.classList.contains('tooltip-close')) {
                el.classList.remove('active');
                return;
            }
            e.stopPropagation();
            document.querySelectorAll('.tooltip').forEach(function(other) {
                if (other !== el) other.classList.remove('active');
            });
            el.classList.toggle('active');
        });
    });
    // Close when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.tooltip').forEach(function(el) {
            el.classList.remove('active');
        });
    });
</script>
</body>
</html>
