<li class="nav-main-item">
    <a class="nav-main-link{{ request()->is('person/datasets/pr2018') ? ' active' : '' }}" href="/person/datasets/pr2018">
        <span class="nav-main-link-name">הכונס הרשמי  פרטיים</span>
    </a>
</li>
<li class="nav-main-item">
    <a class="nav-main-link{{ request()->is('person/datasets/notary') ? ' active' : '' }}" href="/person/datasets/notary">
        <span class="nav-main-link-name">רשימת הנוטריונים</span>
    </a>
</li>
<li class="nav-main-item">
    <a class="nav-main-link{{ request()->is('person/datasets/yerusha') ? ' active' : '' }}" href="/person/datasets/yerusha">
        <span class="nav-main-link-name">בקשות לרשם הירושות</span>
    </a>
</li>
<li class="nav-main-item">
    <a class="nav-main-link{{ request()->is('person/datasets/pinkashakablanim') ? ' active' : '' }}" href="/person/datasets/pinkashakablanim">
        <span class="nav-main-link-name">קבלנים רשומים</span>
    </a>
</li>
<li class="nav-main-item">
    <a class="nav-main-link{{ request()->is('person/datasets/cpalist') ? ' active' : '' }}" href="/person/datasets/cpalist">
        <span class="nav-main-link-name">מרשם רואי חשבון</span>
    </a>
</li>