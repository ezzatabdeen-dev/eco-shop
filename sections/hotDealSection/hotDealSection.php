<!-- Hot Deal Section -->
 <div class="container hotDealSection">
    <div class="hotDealItems">
        <div class="hotDealItemsLeft sm:top">
            <img src="assets/public_img/shop01.png" alt="laptop">
            <img src="assets/public_img/shop03.png" alt="Headphone" class="lg:disable">
        </div>
        <div class="hotDealItemsMiddle">
            <div class="timeDown">
                <ul>
                    <li>
                        <span class="timeNumber text-1" id="dayNumber"></span>
                        <span class="timeName" id="dayName">Days</span>
                    </li>
                    <li>
                        <span class="timeNumber text-1" id="hourNumber"></span>
                        <span class="timeName" id="hourName">Hours</span>
                    </li>
                    <li>
                        <span class="timeNumber text-1" id="minutesNumber"></span>
                        <span class="timeName" id="minutesName">Minuts</span>
                    </li>
                    <li>
                        <span class="timeNumber text-1" id="secoundsNumber"></span>
                        <span class="timeName" id="secoundsName">Secounds</span>
                    </li>
                </ul>
            </div>
            <h2 class="hotDealTitle text-6">hot deal this week</h2>
            <p class="hotDealDiscription text-6">New Collection Up to 50% OFF</p>
            <a href="#" class="shopBtn">
                <span>shop now</span>
            </a>
        </div>
        <div class="hotDealItemsRight sm:disable">
            <img src="assets/public_img/shop03.png" alt="Headphone">
        </div>
    </div>
 </div>

 <script>
  const COUNTDOWN_KEY = "countdownEndTime";
  function resetCountdown() {
    const now = new Date();
    const newCountdownDate = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
    localStorage.setItem(COUNTDOWN_KEY, newCountdownDate.toISOString());
    return newCountdownDate;
  }

  function getCountdownDate() {
    const savedDate = localStorage.getItem(COUNTDOWN_KEY);
    if (savedDate) {
      const countdownDate = new Date(savedDate);
      const now = new Date();
      if (countdownDate > now) {
        return countdownDate;
      }
    }
    return resetCountdown();
  }

  let countdownDate = getCountdownDate();

  function updateCountdown() {
    const now = new Date().getTime();
    const distance = countdownDate.getTime() - now;

    if (distance <= 0) {
      countdownDate = resetCountdown();
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById("dayNumber").innerText = String(days).padStart(2, "0");
    document.getElementById("hourNumber").innerText = String(hours).padStart(2, "0");
    document.getElementById("minutesNumber").innerText = String(minutes).padStart(2, "0");
    document.getElementById("secoundsNumber").innerText = String(seconds).padStart(2, "0");
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);
</script>
