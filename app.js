/**
 * MAMBA VILLAGE NAIROBI — CORE APPLICATION CONTROLLER
 * Pure Vanilla JavaScript (ES6+) — Self-contained, zero dependencies.
 * Fulfills KeMU CISY 401 Undergraduate Capstone Specifications.
 */

// ============================================================================
// 1. DATA INITIALIZATION & STORAGE STATE
// ============================================================================

const STORAGE_KEYS = {
  BOOKINGS: 'mamba_bookings_v1',
  REVIEWS: 'mamba_reviews_v1',
  SUPPLIES: 'mamba_supplies_v1',
  GUIDES: 'mamba_guides_v1',
  EVENTS: 'mamba_events_v1',
  ACTIVE_TICKET: 'mamba_last_ticket_code'
};

const DEFAULT_ATTRACTIONS = [
  {
    id: 'crocs',
    title: 'Giant Nile Crocodile Pits',
    category: 'Wildlife Sanctuary',
    desc: 'Witness over 70 prehistoric Nile crocodiles, including 14-foot apex giants basking, swimming, and lunging during spectacular live feeding sessions.',
    hours: 'Daily: 9:00 AM – 6:00 PM',
    badge: '★ Most Popular',
    icon: '🐊',
    bg: 'linear-gradient(135deg, #103723, #1e5a3c)'
  },
  {
    id: 'boat',
    title: 'Lake Mamba Scenic Boat Rides',
    category: 'Water Adventure',
    desc: 'Cruise around our man-made sanctuary lake in pedal-boats or escorted electric motor crafts, surrounded by water lilies and rare waterfowl.',
    hours: '10:00 AM – 5:30 PM',
    badge: 'Family Favorite',
    icon: '🛶',
    bg: 'linear-gradient(135deg, #0c4a6e, #0284c7)'
  },
  {
    id: 'ostrich',
    title: 'Ostrich & Giant Tortoise Haven',
    category: 'Herbivore Enclosure',
    desc: 'Feed towering East African ostriches under keeper guidance and observe hundred-year-old Aldabra and leopard tortoises roaming peaceful pastures.',
    hours: 'Daily: 9:00 AM – 5:30 PM',
    badge: 'Interactive',
    icon: '🦤',
    bg: 'linear-gradient(135deg, #78350f, #b45309)'
  },
  {
    id: 'botanical',
    title: 'Indigenous Botanical Gardens',
    category: 'Nature & Flora',
    desc: 'Stroll through shaded canopy walkways featuring hundreds of indigenous Kenyan tree species, medicinal plants, and rare tropical succulents.',
    hours: 'Daily: 9:00 AM – 6:00 PM',
    badge: 'Relaxing',
    icon: '🌿',
    bg: 'linear-gradient(135deg, #064e3b, #059669)'
  },
  {
    id: 'amusement',
    title: 'Children’s Amusement Park',
    category: 'Kids & Family',
    desc: 'Equipped with bouncing castles, mini carousels, swings, and climbing sets, designed for children under 12 while parents dine nearby.',
    hours: 'Weekends & Holidays',
    badge: 'Kids Under 12',
    icon: '🎠',
    bg: 'linear-gradient(135deg, #831843, #db2777)'
  },
  {
    id: 'dining',
    title: 'Garden Restaurant & Nyama Choma',
    category: 'Hospitality & Dining',
    desc: 'Renowned for prime Kenyan BBQ (Nyama Choma), traditional sides, refreshing beverages, and the park’s signature exotic crocodile meat delicacy.',
    hours: '11:00 AM – 7:00 PM',
    badge: 'Specialty Dining',
    icon: '🍽️',
    bg: 'linear-gradient(135deg, #451a03, #9a3412)'
  }
];

const DEFAULT_SCHEDULE = [
  {
    id: 'ev-1',
    time: '10:00 AM',
    title: 'Guided Botanical Walk & Medicinal Plant Tour',
    category: 'tour',
    desc: 'Led by veteran naturalist Peter. Meeting at Sanctuary Main Gate.',
    location: 'Botanical Zone D',
    keeper: 'Peter Githinji',
    capacity: '35 Persons',
    isHighlight: false,
    status: 'Scheduled'
  },
  {
    id: 'ev-2',
    time: '11:30 AM',
    title: 'Ostrich Interaction & Giant Egg Examination',
    category: 'feeding',
    desc: 'Hands-on feeding session and educational lecture on ostrich incubation.',
    location: 'Ostrich Yard C',
    keeper: 'Evans Mwangi',
    capacity: '50 Persons',
    isHighlight: false,
    status: 'Scheduled'
  },
  {
    id: 'ev-3',
    time: '01:00 PM',
    title: 'Giant Tortoise Meet & Greet',
    category: 'family',
    desc: 'Ideal for children and photo opportunities with our 100-year-old tortoises.',
    location: 'Tortoise Lawn C',
    keeper: 'Jane Wambui',
    capacity: '40 Persons',
    isHighlight: false,
    status: 'Scheduled'
  },
  {
    id: 'ev-4',
    time: '02:30 PM',
    title: 'MAIN SHOW: Nile Crocodile Feeding Frenzy',
    category: 'feeding',
    desc: 'Watch massive crocodiles burst from water to catch fresh meat from our elevated gallery bridge.',
    location: 'Crocodile Pit 1 & 2',
    keeper: 'Samuel Kiprop (Head Keeper)',
    capacity: '180 Persons',
    isHighlight: true,
    status: 'Upcoming'
  },
  {
    id: 'ev-5',
    time: '03:30 PM',
    title: 'Lake Mamba Scenic Boat Excursion',
    category: 'family',
    desc: 'Waterway tour exploring lake biodiversity and lily ecosystems.',
    location: 'Boat Jetty Zone B',
    keeper: 'Skipper Kevin',
    capacity: '24 Persons',
    isHighlight: false,
    status: 'Scheduled'
  },
  {
    id: 'ev-6',
    time: '04:30 PM',
    title: 'Late Afternoon Crocodile Feeding Showcase',
    category: 'feeding',
    desc: 'Secondary feeding show targeting young hatchlings and intermediate ponds.',
    location: 'Nursery Pond Zone A',
    keeper: 'Samuel Kiprop',
    capacity: '120 Persons',
    isHighlight: true,
    status: 'Scheduled'
  }
];

const DEFAULT_BOOKINGS = [
  {
    ticketCode: 'MV-2026-7841',
    customerName: 'Cyrrus Kipkirui Korir',
    customerEmail: 'cyrrus.korir@kemu.ac.ke',
    customerPhone: '0712345678',
    visitDate: '2026-11-12',
    session: 'Afternoon & Feeding Show',
    passes: { citizenAdult: 2, citizenChild: 1, nonResidentAdult: 0, student: 0 },
    addons: { boatRide: true, feedingPlatform: true, guide: false },
    totalAmount: 1800,
    paymentMethod: 'M-PESA STK Push',
    mpesaRef: 'SJL79X2Q9B',
    paymentStatus: 'Paid',
    gateStatus: 'Confirmed',
    createdAt: '2026-11-11 09:30'
  },
  {
    ticketCode: 'MV-2026-6512',
    customerName: 'Wanjiku Mwangi',
    customerEmail: 'wanjiku.m@gmail.com',
    customerPhone: '0722998877',
    visitDate: '2026-11-11',
    session: 'Morning Session',
    passes: { citizenAdult: 1, citizenChild: 0, nonResidentAdult: 0, student: 1 },
    addons: { boatRide: false, feedingPlatform: false, guide: false },
    totalAmount: 750,
    paymentMethod: 'M-PESA STK Push',
    mpesaRef: 'SJL81A44KL',
    paymentStatus: 'Paid',
    gateStatus: 'Validated',
    createdAt: '2026-11-11 08:15'
  },
  {
    ticketCode: 'MV-2026-9043',
    customerName: 'Mark Thompson (Tour Group)',
    customerEmail: 'm.thompson@travelsafari.uk',
    customerPhone: '0700112233',
    visitDate: '2026-11-13',
    session: 'Full Day Pass',
    passes: { citizenAdult: 0, citizenChild: 0, nonResidentAdult: 4, student: 0 },
    addons: { boatRide: true, feedingPlatform: true, guide: true },
    totalAmount: 10700,
    paymentMethod: 'Visa Card',
    mpesaRef: 'CARD-TX-99042',
    paymentStatus: 'Paid',
    gateStatus: 'Confirmed',
    createdAt: '2026-11-10 16:40'
  }
];

const DEFAULT_GUIDES = [
  { id: 'g1', name: 'John Kiprono', specialty: 'Apex Reptiles & Crocodile Biology', assignedTour: 'St. Austin High School (45 pax)', status: 'In Progress', phone: '+254 711 234 567' },
  { id: 'g2', name: 'Mary Muthoni', specialty: 'Herpetology & Multilingual Tours', assignedTour: 'UK/German Wildlife Delegation (4 pax)', status: 'Scheduled', phone: '+254 722 345 678' },
  { id: 'g3', name: 'Evans Otieno', specialty: 'Botanical & Medicinal Flora', assignedTour: 'Nairobi Family Safari Group (6 pax)', status: 'Scheduled', phone: '+254 733 456 789' },
  { id: 'g4', name: 'Grace Cherono', specialty: 'Birdlife & Lake Mamba Wetland', assignedTour: 'Standby / Boat Jetty Dispatch', status: 'Available', phone: '+254 744 567 890' }
];

const DEFAULT_SUPPLIERS = [
  { po: 'PO-MV-2026-081', supplier: 'Karen Fresh Meats Ltd', item: '250 KG Fresh Beef & Fish (Croc Rations)', qty: '250 KG', date: '2026-11-12', status: 'In Transit' },
  { po: 'PO-MV-2026-079', supplier: 'SafeFloat Marine Supplies', item: '15 Adult & Child Certified Life Jackets', qty: '15 Units', date: '2026-11-10', status: 'Delivered' },
  { po: 'PO-MV-2026-082', supplier: 'Reptile Health Kenya', item: 'Herpetological Vitamin & Calcium Mineral Boosters', qty: '24 Bottles', date: '2026-11-15', status: 'Pending Approval' }
];

const DEFAULT_REVIEWS = [
  { name: 'Faith Wanjiku', rating: 5, category: 'Crocodile Exhibit & Feeding', comment: 'The 2:30 PM crocodile feeding is breathtaking! Standing on the bridge while huge reptiles leap for meat is unforgettable.' },
  { name: 'Brian Ochieng', rating: 5, category: 'Ticketing & Cleanliness', comment: 'Booking tickets online took under 60 seconds with M-Pesa STK push. Scanned our digital QR code at the turnstile seamlessly!' },
  { name: 'Dr. James Kariuki', rating: 5, category: 'Tour Guide Service', comment: 'Guide John was exceptionally knowledgeable on Nile crocodile breeding cycles. Our biology students learned immense value.' },
  { name: 'Sarah van der Berg', rating: 4, category: 'Boat Rides on Lake Mamba', comment: 'Very calm boat ride on Lake Mamba and beautiful botanical gardens. Food at the restaurant was tasty too.' }
];

// Price Constants (in KES)
const TICKET_PRICING = {
  citizenAdult: 500,
  citizenChild: 300,
  nonResidentAdult: 2000,
  student: 250,
  addonBoatRide: 300,
  addonFeedingPlatform: 500,
  addonGuide: 1000
};

// Current Booking State
let bookingState = {
  visitDate: '',
  session: 'afternoon',
  custName: '',
  custEmail: '',
  custPhone: '0712345678',
  passes: {
    citizenAdult: 1,
    citizenChild: 0,
    nonResidentAdult: 0,
    student: 0
  },
  addons: {
    boatRide: false,
    feedingPlatform: false,
    guide: false
  },
  totalAmount: 500
};

// Active Staff Role Perspective
let activeStaffRole = 'admin';

// ============================================================================
// 2. LIFECYCLE & DOM INITIALIZATION
// ============================================================================

document.addEventListener('DOMContentLoaded', () => {
  // Set default visit date to today
  const today = new Date().toISOString().split('T')[0];
  const dateInput = document.getElementById('visitDate');
  if (dateInput) {
    dateInput.value = today;
    dateInput.min = today;
    bookingState.visitDate = today;
    const sumDateEl = document.getElementById('sumDate');
    if (sumDateEl) sumDateEl.textContent = today;
  }

  const grpDateEl = document.getElementById('grpDate');
  if (grpDateEl) {
    grpDateEl.value = today;
    grpDateEl.min = today;
  }

  // Populate data stores
  initializeDataStores();

  // Render components
  renderAttractions();
  renderScheduleTimeline('all');
  renderReviewsWall();
  recalculateTotal();

  // Populate admin views
  renderAdminOverview();
  renderAdminBookingsTable();
  renderFinanceLedger();
  renderTourGuidesRoster();
  renderAdminEventsManager();
  renderSupplierDeliveries();
});

function initializeDataStores() {
  if (!localStorage.getItem(STORAGE_KEYS.BOOKINGS)) {
    localStorage.setItem(STORAGE_KEYS.BOOKINGS, JSON.stringify(DEFAULT_BOOKINGS));
  }
  if (!localStorage.getItem(STORAGE_KEYS.REVIEWS)) {
    localStorage.setItem(STORAGE_KEYS.REVIEWS, JSON.stringify(DEFAULT_REVIEWS));
  }
  if (!localStorage.getItem(STORAGE_KEYS.SUPPLIES)) {
    localStorage.setItem(STORAGE_KEYS.SUPPLIES, JSON.stringify(DEFAULT_SUPPLIERS));
  }
  if (!localStorage.getItem(STORAGE_KEYS.GUIDES)) {
    localStorage.setItem(STORAGE_KEYS.GUIDES, JSON.stringify(DEFAULT_GUIDES));
  }
  if (!localStorage.getItem(STORAGE_KEYS.EVENTS)) {
    localStorage.setItem(STORAGE_KEYS.EVENTS, JSON.stringify(DEFAULT_SCHEDULE));
  }
}

function getStoredData(key) {
  try {
    const raw = localStorage.getItem(key);
    return raw ? JSON.parse(raw) : [];
  } catch (e) {
    console.error('Storage parse error:', e);
    return [];
  }
}

function setStoredData(key, data) {
  try {
    localStorage.setItem(key, JSON.stringify(data));
  } catch (e) {
    console.error('Storage write error:', e);
  }
}

// ============================================================================
// 3. VISITOR PORTAL RENDERING
// ============================================================================

function renderAttractions() {
  const container = document.getElementById('attractionsContainer');
  if (!container) return;

  container.innerHTML = DEFAULT_ATTRACTIONS.map(item => `
    <article class="attraction-card">
      <div class="attraction-media" style="background: ${item.bg};">
        <span class="attraction-pill">${item.category}</span>
        <div style="font-size: 3.5rem; position: absolute; top: 1.25rem; right: 1.25rem; opacity: 0.85;">
          ${item.icon}
        </div>
      </div>
      <div class="attraction-body">
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem;">
          <h3>${item.title}</h3>
          <span class="badge badge-gold">${item.badge}</span>
        </div>
        <p>${item.desc}</p>
        <div class="attraction-footer">
          <span class="attraction-hours">🕒 ${item.hours}</span>
          <button class="btn btn-outline btn-small" onclick="scrollToBooking()">Book Entry</button>
        </div>
      </div>
    </article>
  `).join('');
}

function renderScheduleTimeline(filter = 'all') {
  const container = document.getElementById('scheduleTimeline');
  if (!container) return;

  const events = getStoredData(STORAGE_KEYS.EVENTS);
  const filtered = filter === 'all' ? events : events.filter(e => e.category === filter);

  if (filtered.length === 0) {
    container.innerHTML = `<p class="text-center text-muted" style="padding: 2rem;">No scheduled events under this category.</p>`;
    return;
  }

  container.innerHTML = filtered.map(item => `
    <div class="timeline-item ${item.isHighlight ? 'highlight-event' : ''}">
      <div class="timeline-node"></div>
      <div class="timeline-card">
        <div style="flex: 1;">
          <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span class="timeline-time">${item.time}</span>
            <span class="badge ${item.isHighlight ? 'badge-gold' : 'badge-green'}">${item.location}</span>
          </div>
          <h4 class="timeline-title">${item.title}</h4>
          <p class="timeline-desc">${item.desc}</p>
          <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.4rem;">
            Keeper: <strong>${item.keeper}</strong> | Capacity: <strong>${item.capacity}</strong>
          </div>
        </div>
        <div class="timeline-action">
          <button class="btn btn-outline btn-small" onclick="setVisitReminder('${item.title}', '${item.time}')">
            🔔 Set Reminder
          </button>
        </div>
      </div>
    </div>
  `).join('');
}

function filterSchedule(category) {
  document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
  renderScheduleTimeline(category);
}

function setVisitReminder(title, time) {
  showToast(`Reminder set for "${title}" at ${time}. We'll notify you on your mobile pass!`, 'success');
}

function renderReviewsWall() {
  const container = document.getElementById('reviewsWall');
  if (!container) return;

  const reviews = getStoredData(STORAGE_KEYS.REVIEWS);
  container.innerHTML = reviews.map(rev => `
    <div class="review-card">
      <div class="review-header">
        <span class="review-author">${rev.name}</span>
        <span class="review-stars">${'★'.repeat(rev.rating)}${'☆'.repeat(5 - rev.rating)}</span>
      </div>
      <span class="review-category">${rev.category}</span>
      <p class="review-comment">"${rev.comment}"</p>
    </div>
  `).join('');
}

function setRating(rating) {
  document.getElementById('fbRatingVal').value = rating;
  const stars = document.querySelectorAll('.star-rating-select .star');
  stars.forEach((star, index) => {
    if (index < rating) {
      star.classList.add('active');
    } else {
      star.classList.remove('active');
    }
  });
}

function submitVisitorFeedback(event) {
  event.preventDefault();
  const name = document.getElementById('fbName').value.trim();
  const rating = parseInt(document.getElementById('fbRatingVal').value, 10);
  const category = document.getElementById('fbCategory').value;
  const comment = document.getElementById('fbComment').value.trim();

  if (!name || !comment) {
    showToast('Please fill in both your name and feedback comment.', 'error');
    return;
  }

  const reviews = getStoredData(STORAGE_KEYS.REVIEWS);
  reviews.unshift({ name, rating, category, comment });
  setStoredData(STORAGE_KEYS.REVIEWS, reviews);

  renderReviewsWall();
  document.getElementById('feedbackForm').reset();
  setRating(5);
  showToast('Thank you! Your feedback has been published.', 'success');
}

function handleGroupInquiry(event) {
  event.preventDefault();
  const orgName = document.getElementById('grpOrgName').value;
  const headcount = document.getElementById('grpHeadcount').value;
  const date = document.getElementById('grpDate').value;

  showToast(`Group quote request for ${orgName} (${headcount} persons on ${date}) submitted to Service Manager!`, 'success');
  event.target.reset();
}

// Interactive Map Zone Info
const ZONE_INFO = {
  croc: {
    title: 'Zone A: Giant Nile Crocodile Sanctuary',
    desc: 'The central attraction featuring 70+ Nile crocodiles across breeding ponds, nursery enclosures, and the elevated safety observation bridge where daily feeding frenzies take place.'
  },
  lake: {
    title: 'Zone B: Lake Mamba & Boat Jetty',
    desc: 'Tranquil man-made lake housing peaceful bird species, aquatic lilies, and a fleet of pedal and motor boats operated under strict maritime life-jacket safety regulations.'
  },
  ostrich: {
    title: 'Zone C: Ostrich Enclosure & Tortoise Sanctuary',
    desc: 'Safe walkthrough enclosure featuring towering flightless African ostriches and the centuries-old Aldabra & leopard tortoises. Children can hand-feed greens under keeper supervision.'
  },
  botanical: {
    title: 'Zone D: Botanical Garden & Canopy Walk',
    desc: 'Over 15 acres of conserved indigenous flora, shaded gazebos, medicinal trees used in traditional African pharmacopeia, and botanical research collections.'
  },
  dining: {
    title: 'Zone E: Garden Restaurant & Event Lawns',
    desc: 'Spacious outdoor restaurant famous for traditional Nyama Choma, fresh fruit blends, and manicured lawns suitable for birthday celebrations, weddings, and school picnics.'
  }
};

function showZoneInfo(zoneKey) {
  const card = document.getElementById('zoneDetailCard');
  if (!card || !ZONE_INFO[zoneKey]) return;
  card.innerHTML = `
    <strong>${ZONE_INFO[zoneKey].title}</strong>
    <p>${ZONE_INFO[zoneKey].desc}</p>
  `;
}

function toggleFaq(el) {
  el.classList.toggle('active');
}

// ============================================================================
// 4. TICKETING & CALCULATOR ENGINE (Modules 1.5.7, 1.5.10, 1.5.13)
// ============================================================================

function updateTicketQty(tierKey, delta) {
  const current = bookingState.passes[tierKey] || 0;
  const next = Math.max(0, current + delta);
  bookingState.passes[tierKey] = next;

  const displayEl = document.getElementById(`qty_${tierKey}`);
  if (displayEl) displayEl.textContent = next;

  recalculateTotal();
}

function recalculateTotal() {
  const boatCheck = document.getElementById('addonBoatRide');
  const feedingCheck = document.getElementById('addonFeedingPlatform');
  const guideCheck = document.getElementById('addonGuide');

  bookingState.addons.boatRide = boatCheck ? boatCheck.checked : false;
  bookingState.addons.feedingPlatform = feedingCheck ? feedingCheck.checked : false;
  bookingState.addons.guide = guideCheck ? guideCheck.checked : false;

  // Calculate passes total
  let subtotal = 0;
  let lineItemsHtml = '';
  let totalPeople = 0;

  for (const [tier, qty] of Object.entries(bookingState.passes)) {
    if (qty > 0) {
      const price = TICKET_PRICING[tier];
      const lineCost = qty * price;
      subtotal += lineCost;
      totalPeople += qty;

      let label = '';
      if (tier === 'citizenAdult') label = 'Citizen / Resident Adult';
      if (tier === 'citizenChild') label = 'Citizen Child (3–12 yrs)';
      if (tier === 'nonResidentAdult') label = 'Non-Resident Tourist Adult';
      if (tier === 'student') label = 'Student ID Pass';

      lineItemsHtml += `
        <div class="summary-row">
          <span>${qty}x ${label}</span>
          <strong>KES ${lineCost.toLocaleString()}</strong>
        </div>
      `;
    }
  }

  // Activity Add-ons
  if (bookingState.addons.boatRide) {
    const boatCost = totalPeople > 0 ? totalPeople * TICKET_PRICING.addonBoatRide : TICKET_PRICING.addonBoatRide;
    subtotal += boatCost;
    lineItemsHtml += `
      <div class="summary-row text-gold">
        <span>🛶 Lake Boat Ride (${totalPeople > 0 ? totalPeople + ' pax' : 'Single'})</span>
        <strong>KES ${boatCost.toLocaleString()}</strong>
      </div>
    `;
  }

  if (bookingState.addons.feedingPlatform) {
    subtotal += TICKET_PRICING.addonFeedingPlatform;
    lineItemsHtml += `
      <div class="summary-row text-gold">
        <span>🥩 VIP Feeding Bridge Access</span>
        <strong>KES ${TICKET_PRICING.addonFeedingPlatform.toLocaleString()}</strong>
      </div>
    `;
  }

  if (bookingState.addons.guide) {
    subtotal += TICKET_PRICING.addonGuide;
    lineItemsHtml += `
      <div class="summary-row text-gold">
        <span>🧭 Private Dedicated Tour Guide</span>
        <strong>KES ${TICKET_PRICING.addonGuide.toLocaleString()}</strong>
      </div>
    `;
  }

  if (subtotal === 0) {
    lineItemsHtml = `<p class="text-muted" style="padding: 0.5rem 0;">No tickets selected yet. Choose at least one admission pass above.</p>`;
  }

  bookingState.totalAmount = subtotal;

  // Update UI Elements
  const container = document.getElementById('summaryLineItems');
  if (container) container.innerHTML = lineItemsHtml;

  const sumSubtotalEl = document.getElementById('sumSubtotal');
  if (sumSubtotalEl) sumSubtotalEl.textContent = `KES ${subtotal.toLocaleString()}`;

  const sumGrandTotalEl = document.getElementById('sumGrandTotal');
  if (sumGrandTotalEl) sumGrandTotalEl.textContent = `KES ${subtotal.toLocaleString()}`;

  // Update date & session summary
  const dateInput = document.getElementById('visitDate');
  if (dateInput && dateInput.value) {
    bookingState.visitDate = dateInput.value;
    const sumDateEl = document.getElementById('sumDate');
    if (sumDateEl) sumDateEl.textContent = dateInput.value;
  }

  const sessionSelect = document.getElementById('visitSession');
  if (sessionSelect) {
    bookingState.session = sessionSelect.options[sessionSelect.selectedIndex].text;
    const sumSessionEl = document.getElementById('sumSession');
    if (sumSessionEl) sumSessionEl.textContent = bookingState.session;
  }
}

function initiateCheckout() {
  if (bookingState.totalAmount <= 0) {
    showToast('Please select at least 1 admission ticket before proceeding to payment.', 'warning');
    return;
  }

  const custName = document.getElementById('custName').value.trim();
  const custEmail = document.getElementById('custEmail').value.trim();
  const custPhone = document.getElementById('custPhone').value.trim();

  bookingState.custName = custName;
  bookingState.custEmail = custEmail;
  bookingState.custPhone = custPhone;

  // Prepare Checkout Modal
  document.getElementById('modalPayAmountMpesa').textContent = `KES ${bookingState.totalAmount.toLocaleString()}`;
  document.getElementById('modalPayAmountCard').textContent = `KES ${bookingState.totalAmount.toLocaleString()}`;
  document.getElementById('mpesaConfirmPhone').value = custPhone || '0712345678';

  openModal('checkoutModal');
}

function switchPaymentMethod(method) {
  const mpesaBtn = document.getElementById('tabMpesaBtn');
  const cardBtn = document.getElementById('tabCardBtn');
  const mpesaPane = document.getElementById('payMpesaPane');
  const cardPane = document.getElementById('payCardPane');

  if (method === 'mpesa') {
    mpesaBtn.classList.add('active');
    cardBtn.classList.remove('active');
    mpesaPane.style.display = 'block';
    cardPane.style.display = 'none';
  } else {
    cardBtn.classList.add('active');
    mpesaBtn.classList.remove('active');
    cardPane.style.display = 'block';
    mpesaPane.style.display = 'none';
  }
}

// Simulated Lipa Na M-Pesa STK Push
function triggerMpesaPush() {
  const btn = document.getElementById('btnTriggerStk');
  const waitingBox = document.getElementById('stkPushWaiting');
  const countdownEl = document.getElementById('stkCountdown');
  const phone = document.getElementById('mpesaConfirmPhone').value;

  btn.style.display = 'none';
  waitingBox.style.display = 'block';

  let seconds = 5;
  countdownEl.textContent = `Awaiting PIN on ${phone}: ${seconds}s`;

  const timer = setInterval(() => {
    seconds--;
    if (seconds > 0) {
      countdownEl.textContent = `Awaiting PIN on ${phone}: ${seconds}s`;
    } else {
      clearInterval(timer);
      waitingBox.style.display = 'none';
      btn.style.display = 'block';

      // Complete payment
      const mpesaRef = 'SJL' + Math.random().toString(36).substring(2, 8).toUpperCase();
      completeBookingOrder('M-PESA STK Push', mpesaRef);
    }
  }, 1000);
}

function triggerCardPayment() {
  const cardRef = 'CARD-' + Math.random().toString(36).substring(2, 8).toUpperCase();
  completeBookingOrder('Visa/Mastercard', cardRef);
}

function completeBookingOrder(paymentMethod, referenceCode) {
  closeModal('checkoutModal');

  const ticketCode = 'MV-' + new Date().getFullYear() + '-' + Math.floor(1000 + Math.random() * 9000);
  const now = new Date();
  const timestamp = `${now.toISOString().split('T')[0]} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;

  const newBooking = {
    ticketCode,
    customerName: bookingState.custName || 'Guest Visitor',
    customerEmail: bookingState.custEmail || 'visitor@example.com',
    customerPhone: bookingState.custPhone || '0712345678',
    visitDate: bookingState.visitDate,
    session: bookingState.session,
    passes: { ...bookingState.passes },
    addons: { ...bookingState.addons },
    totalAmount: bookingState.totalAmount,
    paymentMethod,
    mpesaRef: referenceCode,
    paymentStatus: 'Paid',
    gateStatus: 'Confirmed',
    createdAt: timestamp
  };

  const bookings = getStoredData(STORAGE_KEYS.BOOKINGS);
  bookings.unshift(newBooking);
  setStoredData(STORAGE_KEYS.BOOKINGS, bookings);
  localStorage.setItem(STORAGE_KEYS.ACTIVE_TICKET, ticketCode);

  showToast(`🎉 Payment Confirmed via ${paymentMethod}! Receipt: ${referenceCode}`, 'success');

  // Re-render admin panels
  renderAdminOverview();
  renderAdminBookingsTable();
  renderFinanceLedger();

  // Open ticket modal
  renderDigitalTicket(newBooking);
  openModal('ticketWalletModal');
}

// Generate QR Code Pass (Pure SVG Pattern)
function generateSVGQRCode(code) {
  return `
    <svg class="qr-code-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
      <rect width="100" height="100" fill="#ffffff"/>
      <!-- QR Position Markers -->
      <rect x="5" y="5" width="26" height="26" fill="#0f271d"/>
      <rect x="8" y="8" width="20" height="20" fill="#ffffff"/>
      <rect x="11" y="11" width="14" height="14" fill="#0f271d"/>

      <rect x="69" y="5" width="26" height="26" fill="#0f271d"/>
      <rect x="72" y="8" width="20" height="20" fill="#ffffff"/>
      <rect x="75" y="11" width="14" height="14" fill="#0f271d"/>

      <rect x="5" y="69" width="26" height="26" fill="#0f271d"/>
      <rect x="8" y="72" width="20" height="20" fill="#ffffff"/>
      <rect x="11" y="75" width="14" height="14" fill="#0f271d"/>

      <!-- Stylized Data Matrix Dots based on hash -->
      <rect x="36" y="8" width="6" height="6" fill="#0f271d"/>
      <rect x="46" y="14" width="6" height="6" fill="#0f271d"/>
      <rect x="56" y="8" width="6" height="6" fill="#0f271d"/>
      <rect x="36" y="24" width="6" height="6" fill="#0f271d"/>
      <rect x="46" y="24" width="16" height="6" fill="#0f271d"/>

      <rect x="8" y="36" width="6" height="6" fill="#0f271d"/>
      <rect x="18" y="42" width="10" height="6" fill="#0f271d"/>
      <rect x="8" y="54" width="6" height="6" fill="#0f271d"/>
      <rect x="22" y="54" width="6" height="6" fill="#0f271d"/>

      <rect x="36" y="36" width="28" height="28" fill="#10b981" rx="4"/>
      <text x="50" y="54" font-size="12" font-weight="bold" fill="#ffffff" text-anchor="middle">MV</text>

      <rect x="68" y="36" width="8" height="6" fill="#0f271d"/>
      <rect x="82" y="42" width="10" height="6" fill="#0f271d"/>
      <rect x="74" y="54" width="18" height="6" fill="#0f271d"/>

      <rect x="36" y="68" width="14" height="6" fill="#0f271d"/>
      <rect x="56" y="74" width="6" height="18" fill="#0f271d"/>
      <rect x="42" y="86" width="10" height="6" fill="#0f271d"/>
      <rect x="68" y="82" width="24" height="6" fill="#0f271d"/>
    </svg>
  `;
}

function renderDigitalTicket(booking) {
  const container = document.getElementById('digitalTicketBody');
  if (!container) return;

  const passesSummary = Object.entries(booking.passes)
    .filter(([_, qty]) => qty > 0)
    .map(([tier, qty]) => `${qty}x ${tier.replace(/([A-Z])/g, ' $1')}`)
    .join(', ');

  const addonsSummary = Object.entries(booking.addons)
    .filter(([_, val]) => val === true)
    .map(([addon]) => addon.replace(/([A-Z])/g, ' $1'))
    .join(', ') || 'Standard Entry';

  container.innerHTML = `
    <div class="ticket-pass-card">
      <div class="ticket-pass-header">
        <div>
          <div style="font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase; color: #a7f3d0;">Official Admission Pass</div>
          <h2 style="color: #ffffff; margin: 0; font-size: 1.4rem;">MAMBA VILLAGE NAIROBI</h2>
          <span style="font-size: 0.75rem; color: #cbd5e1;">CISY 401 Capstone E-Ticketing System</span>
        </div>
        <div style="text-align: right;">
          <span class="badge ${booking.gateStatus === 'Validated' ? 'badge-red' : 'badge-green'}">
            ● ${booking.gateStatus}
          </span>
          <div style="font-size: 0.75rem; color: #cbd5e1; margin-top: 0.25rem;">REF: ${booking.mpesaRef}</div>
        </div>
      </div>

      <div class="ticket-pass-body">
        <div class="ticket-details-col">
          <div class="ticket-data-row">
            <div class="ticket-label">Guest / Lead Visitor</div>
            <div class="ticket-val">${booking.customerName}</div>
          </div>
          <div class="ticket-data-row" style="display: flex; gap: 1.5rem;">
            <div>
              <div class="ticket-label">Visit Date</div>
              <div class="ticket-val">${booking.visitDate}</div>
            </div>
            <div>
              <div class="ticket-label">Arrival Window</div>
              <div class="ticket-val" style="font-size: 0.95rem;">${booking.session}</div>
            </div>
          </div>
          <div class="ticket-data-row">
            <div class="ticket-label">Passes Included</div>
            <div class="ticket-val" style="font-size: 0.95rem;">${passesSummary}</div>
          </div>
          <div class="ticket-data-row">
            <div class="ticket-label">Experience Add-ons</div>
            <div class="ticket-val" style="font-size: 0.95rem; color: #d97706;">${addonsSummary}</div>
          </div>
          <div class="ticket-data-row" style="display: flex; justify-content: space-between; border-top: 1px solid #e2e8f0; padding-top: 0.75rem;">
            <div>
              <div class="ticket-label">Total Settled</div>
              <div class="ticket-val text-green">KES ${booking.totalAmount.toLocaleString()}</div>
            </div>
            <div>
              <div class="ticket-label">Channel</div>
              <div class="ticket-val" style="font-size: 0.9rem;">${booking.paymentMethod}</div>
            </div>
          </div>
        </div>

        <div class="ticket-qr-block">
          ${generateSVGQRCode(booking.ticketCode)}
          <div style="font-family: monospace; font-weight: 800; font-size: 1rem; color: #0f271d; margin-top: 0.5rem;">
            ${booking.ticketCode}
          </div>
          <span style="font-size: 0.7rem; color: #64748b; text-align: center; margin-top: 0.25rem;">
            Present to turnstile scanner or staff marshal
          </span>
        </div>
      </div>
    </div>
  `;
}

function openTicketWalletModal() {
  const bookings = getStoredData(STORAGE_KEYS.BOOKINGS);
  if (bookings.length === 0) {
    showToast('No active digital tickets found on this device. Book a ticket first!', 'warning');
    scrollToBooking();
    return;
  }
  const lastCode = localStorage.getItem(STORAGE_KEYS.ACTIVE_TICKET);
  const activeBooking = bookings.find(b => b.ticketCode === lastCode) || bookings[0];

  renderDigitalTicket(activeBooking);
  openModal('ticketWalletModal');
}

// ============================================================================
// 5. GLOBAL SEARCH (Module 16)
// ============================================================================

function handleParkSearch(term) {
  const panel = document.getElementById('searchResultsDropdown');
  const clearBtn = document.getElementById('searchClearBtn');
  if (!panel) return;

  const q = term.toLowerCase().trim();
  if (!q) {
    panel.style.display = 'none';
    if (clearBtn) clearBtn.style.display = 'none';
    return;
  }

  if (clearBtn) clearBtn.style.display = 'block';

  // Search through attractions, schedule, FAQs
  const attractions = DEFAULT_ATTRACTIONS.filter(a =>
    a.title.toLowerCase().includes(q) || a.desc.toLowerCase().includes(q) || a.category.toLowerCase().includes(q)
  );

  const events = getStoredData(STORAGE_KEYS.EVENTS).filter(e =>
    e.title.toLowerCase().includes(q) || e.desc.toLowerCase().includes(q) || e.location.toLowerCase().includes(q)
  );

  let resultsHtml = '';

  if (attractions.length === 0 && events.length === 0) {
    resultsHtml = `<div style="padding: 1rem; text-align: center; color: #64748b;">No results found for "${term}". Try "crocodile", "feeding", or "boat".</div>`;
  } else {
    attractions.forEach(a => {
      resultsHtml += `
        <div class="search-result-item" onclick="scrollToElement('attractions'); clearParkSearch();">
          <div>
            <strong>${a.icon} ${a.title}</strong>
            <div style="font-size: 0.75rem; color: #64748b;">${a.category} • ${a.hours}</div>
          </div>
          <span class="badge badge-green">Attraction</span>
        </div>
      `;
    });

    events.forEach(e => {
      resultsHtml += `
        <div class="search-result-item" onclick="scrollToElement('feeding'); clearParkSearch();">
          <div>
            <strong>🕒 ${e.time} — ${e.title}</strong>
            <div style="font-size: 0.75rem; color: #64748b;">${e.location} • Keeper: ${e.keeper}</div>
          </div>
          <span class="badge badge-gold">Event Slot</span>
        </div>
      `;
    });
  }

  panel.innerHTML = resultsHtml;
  panel.style.display = 'block';
}

function quickSearch(keyword) {
  const input = document.getElementById('parkSearchInput');
  if (!input) return;
  input.value = keyword;
  handleParkSearch(keyword);
}

function clearParkSearch() {
  const input = document.getElementById('parkSearchInput');
  const panel = document.getElementById('searchResultsDropdown');
  const clearBtn = document.getElementById('searchClearBtn');
  if (input) input.value = '';
  if (panel) panel.style.display = 'none';
  if (clearBtn) clearBtn.style.display = 'none';
}

// ============================================================================
// 6. STAFF & ADMINISTRATOR CONTROL CENTER (Modules 2 - 14)
// ============================================================================

function switchMainView(view) {
  const visitorContainer = document.getElementById('visitorViewContainer');
  const staffContainer = document.getElementById('staffViewContainer');
  const btnVisitor = document.getElementById('btnVisitorView');
  const btnStaff = document.getElementById('btnStaffView');

  if (view === 'staff') {
    visitorContainer.style.display = 'none';
    staffContainer.style.display = 'block';
    btnStaff.classList.add('active');
    btnVisitor.classList.remove('active');
    renderAdminOverview();
  } else {
    staffContainer.style.display = 'none';
    visitorContainer.style.display = 'block';
    btnVisitor.classList.add('active');
    btnStaff.classList.remove('active');
  }
}

function switchAdminTab(tabId) {
  document.querySelectorAll('.admin-nav-item').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.admin-tab-pane').forEach(pane => pane.classList.remove('active'));

  const activePane = document.getElementById(`tab_${tabId}`);
  if (activePane) activePane.classList.add('active');

  // Highlight corresponding button
  event.target.classList.add('active');

  // Update header text based on tab
  const headers = {
    dashOverview: 'Park Operations Overview',
    dashBookings: 'Visitor Admissions & Gate Scanner',
    dashFinance: 'Finance, M-Pesa Ledger & Invoices',
    dashTours: 'Tour Guide Allocations & Roster',
    dashEvents: 'Feeding Timetable & Event Planning',
    dashSuppliers: 'Park Supply Requisitions & Deliveries',
    dashReports: 'Executive Statistical Analytics'
  };
  const headerEl = document.getElementById('adminSectionHeader');
  if (headerEl && headers[tabId]) headerEl.textContent = headers[tabId];
}

function changeStaffPerspective(role) {
  activeStaffRole = role;
  const roleTitles = {
    admin: 'Super Administrator',
    finance: 'Finance & Accounts Manager',
    events: 'Park Events & Show Director',
    guide: 'Head Tour Guide Officer',
    service: 'Guest Services Coordinator',
    supplier: 'Logistics & Supply Superintendent'
  };
  const titleEl = document.getElementById('currentRoleTitle');
  if (titleEl && roleTitles[role]) {
    titleEl.textContent = roleTitles[role];
  }
  showToast(`Switched view to ${roleTitles[role]} perspective.`, 'success');
}

function renderAdminOverview() {
  const bookings = getStoredData(STORAGE_KEYS.BOOKINGS);
  const events = getStoredData(STORAGE_KEYS.EVENTS);

  // Calculate stats
  let totalRevenue = 0;
  let totalVisitors = 0;

  bookings.forEach(b => {
    totalRevenue += b.totalAmount || 0;
    const passes = b.passes || {};
    totalVisitors += (passes.citizenAdult || 0) + (passes.citizenChild || 0) + (passes.nonResidentAdult || 0) + (passes.student || 0);
  });

  const kpiVisitors = document.getElementById('kpiVisitors');
  const kpiRevenue = document.getElementById('kpiRevenue');
  if (kpiVisitors) kpiVisitors.textContent = totalVisitors > 0 ? totalVisitors : '142';
  if (kpiRevenue) kpiRevenue.textContent = `KES ${totalRevenue.toLocaleString()}`;

  // Mini schedule queue
  const schedBody = document.getElementById('adminScheduleTable');
  if (schedBody) {
    schedBody.innerHTML = events.slice(0, 4).map(e => `
      <tr>
        <td><strong>${e.time}</strong></td>
        <td>${e.title}</td>
        <td><span class="badge badge-green">${e.location}</span></td>
        <td><span class="badge ${e.isHighlight ? 'badge-gold' : 'badge-blue'}">${e.status}</span></td>
      </tr>
    `).join('');
  }

  // Recent bookings preview
  const recBody = document.getElementById('adminRecentBookings');
  if (recBody) {
    recBody.innerHTML = bookings.slice(0, 5).map(b => `
      <tr>
        <td><code>${b.ticketCode}</code></td>
        <td>${b.customerName}</td>
        <td><strong>KES ${b.totalAmount.toLocaleString()}</strong></td>
        <td><span class="badge badge-green">${b.paymentMethod.includes('M-PESA') ? 'M-Pesa' : 'Card'}</span></td>
        <td><span class="badge ${b.gateStatus === 'Validated' ? 'badge-red' : 'badge-green'}">${b.gateStatus}</span></td>
      </tr>
    `).join('');
  }
}

function renderAdminBookingsTable(filterText = '') {
  const container = document.getElementById('allBookingsTableBody');
  if (!container) return;

  const bookings = getStoredData(STORAGE_KEYS.BOOKINGS);
  const q = filterText.toLowerCase();

  const filtered = bookings.filter(b =>
    b.ticketCode.toLowerCase().includes(q) ||
    b.customerName.toLowerCase().includes(q) ||
    b.customerPhone.includes(q)
  );

  container.innerHTML = filtered.map(b => {
    const passes = Object.entries(b.passes)
      .filter(([_, qty]) => qty > 0)
      .map(([tier, qty]) => `${qty}x ${tier.replace(/([A-Z])/g, ' $1')}`)
      .join(', ');

    return `
      <tr>
        <td><code>${b.ticketCode}</code></td>
        <td><strong>${b.customerName}</strong><br><small class="text-muted">${b.customerEmail}</small></td>
        <td>${b.customerPhone}</td>
        <td>${b.visitDate}</td>
        <td><small>${passes}</small></td>
        <td><strong>KES ${b.totalAmount.toLocaleString()}</strong></td>
        <td><code>${b.mpesaRef}</code></td>
        <td>
          <span class="badge ${b.gateStatus === 'Validated' ? 'badge-red' : 'badge-green'}">
            ${b.gateStatus}
          </span>
        </td>
        <td>
          ${b.gateStatus === 'Confirmed' ? `
            <button class="btn btn-outline btn-small" onclick="validateTicketCodeDirect('${b.ticketCode}')">
              ✓ Admit Guest
            </button>
          ` : `
            <span class="text-muted" style="font-size: 0.8rem;">Admitted</span>
          `}
        </td>
      </tr>
    `;
  }).join('');
}

function filterBookingsTable(term) {
  renderAdminBookingsTable(term);
}

function renderFinanceLedger() {
  const container = document.getElementById('financeLedgerBody');
  if (!container) return;

  const bookings = getStoredData(STORAGE_KEYS.BOOKINGS);
  let mpesaSum = 0;
  let cardSum = 0;

  bookings.forEach(b => {
    if (b.paymentMethod.includes('M-PESA')) mpesaSum += b.totalAmount;
    else cardSum += b.totalAmount;
  });

  const mpesaEl = document.getElementById('finMpesaTotal');
  const cardEl = document.getElementById('finCardTotal');
  if (mpesaEl) mpesaEl.textContent = `KES ${mpesaSum.toLocaleString()}`;
  if (cardEl) cardEl.textContent = `KES ${cardSum.toLocaleString()}`;

  container.innerHTML = bookings.map((b, idx) => `
    <tr>
      <td><code>MV-INV-${202600 + idx}</code></td>
      <td>${b.createdAt || b.visitDate}</td>
      <td><strong>${b.customerName}</strong></td>
      <td><span class="badge badge-green">${b.paymentMethod}</span></td>
      <td><code>${b.mpesaRef}</code></td>
      <td><strong>KES ${b.totalAmount.toLocaleString()}</strong></td>
      <td><span class="badge badge-green">Reconciled 100%</span></td>
      <td>
        <button class="link-btn" onclick="openTicketWalletModal()">View Receipt</button>
      </td>
    </tr>
  `).join('');
}

function renderTourGuidesRoster() {
  const container = document.getElementById('tourGuidesGrid');
  if (!container) return;

  const guides = getStoredData(STORAGE_KEYS.GUIDES);
  container.innerHTML = guides.map(g => `
    <div class="guide-card">
      <div class="guide-header">
        <span class="guide-name">🧭 ${g.name}</span>
        <span class="badge ${g.status === 'In Progress' ? 'badge-gold' : 'badge-green'}">${g.status}</span>
      </div>
      <p style="font-size: 0.85rem; color: #475569; margin-bottom: 0.5rem;">
        <strong>Specialty:</strong> ${g.specialty}
      </p>
      <div style="background: #ffffff; padding: 0.65rem; border-radius: 4px; border: 1px solid #e2e8f0; font-size: 0.8rem; margin-bottom: 0.75rem;">
        <strong>Active Tour Assignment:</strong><br>
        ${g.assignedTour}
      </div>
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.75rem; color: #64748b;">${g.phone}</span>
        <button class="btn btn-outline btn-small" onclick="toggleGuideStatus('${g.id}')">
          Toggle Status
        </button>
      </div>
    </div>
  `).join('');
}

function toggleGuideStatus(guideId) {
  const guides = getStoredData(STORAGE_KEYS.GUIDES);
  const guide = guides.find(g => g.id === guideId);
  if (guide) {
    guide.status = guide.status === 'In Progress' ? 'Available' : 'In Progress';
    setStoredData(STORAGE_KEYS.GUIDES, guides);
    renderTourGuidesRoster();
    showToast(`Updated status for Guide ${guide.name} to ${guide.status}`, 'success');
  }
}

function promptAddGuideAssignment() {
  const name = prompt('Enter guide name to dispatch (e.g. Samuel Kiprop):');
  if (!name) return;
  const tour = prompt('Enter assigned group / tour details:');
  if (!tour) return;

  const guides = getStoredData(STORAGE_KEYS.GUIDES);
  guides.push({
    id: 'g' + (guides.length + 1),
    name,
    specialty: 'Reptilian Wildlife Ecology',
    assignedTour: tour,
    status: 'In Progress',
    phone: '+254 700 ' + Math.floor(100000 + Math.random() * 900000)
  });
  setStoredData(STORAGE_KEYS.GUIDES, guides);
  renderTourGuidesRoster();
  showToast(`Guide ${name} dispatched successfully!`, 'success');
}

function renderAdminEventsManager() {
  const container = document.getElementById('adminEventsManagerBody');
  if (!container) return;

  const events = getStoredData(STORAGE_KEYS.EVENTS);
  container.innerHTML = events.map(e => `
    <tr>
      <td><strong>${e.title}</strong></td>
      <td><code>${e.time}</code></td>
      <td>${e.location}</td>
      <td>${e.keeper}</td>
      <td>${e.capacity}</td>
      <td><span class="badge ${e.isHighlight ? 'badge-gold' : 'badge-green'}">${e.status}</span></td>
      <td>
        <button class="btn btn-outline btn-small" onclick="deleteEventSlot('${e.id}')">Remove</button>
      </td>
    </tr>
  `).join('');
}

function deleteEventSlot(id) {
  let events = getStoredData(STORAGE_KEYS.EVENTS);
  events = events.filter(e => e.id !== id);
  setStoredData(STORAGE_KEYS.EVENTS, events);
  renderAdminEventsManager();
  renderScheduleTimeline('all');
  showToast('Show slot removed from park schedule.', 'success');
}

function openCreateEventModal() {
  const title = prompt('Enter Show / Activity Title (e.g., Baby Crocodile Hatchling Talk):');
  if (!title) return;
  const time = prompt('Enter time slot (e.g., 03:00 PM):');
  if (!time) return;

  const events = getStoredData(STORAGE_KEYS.EVENTS);
  events.push({
    id: 'ev-' + (events.length + 1),
    time,
    title,
    category: 'feeding',
    desc: 'Auditorium wildlife showcase and keeper demonstration.',
    location: 'Amphitheater Zone D',
    keeper: 'Samuel Kiprop',
    capacity: '80 Persons',
    isHighlight: false,
    status: 'Scheduled'
  });
  setStoredData(STORAGE_KEYS.EVENTS, events);
  renderAdminEventsManager();
  renderScheduleTimeline('all');
  showToast(`New show slot "${title}" added to schedule!`, 'success');
}

function renderSupplierDeliveries() {
  const container = document.getElementById('supplierOrdersBody');
  if (!container) return;

  const supplies = getStoredData(STORAGE_KEYS.SUPPLIES);
  container.innerHTML = supplies.map(s => `
    <tr>
      <td><code>${s.po}</code></td>
      <td><strong>${s.supplier}</strong></td>
      <td>${s.item}</td>
      <td>${s.qty}</td>
      <td>${s.date}</td>
      <td>
        <span class="badge ${s.status === 'Delivered' ? 'badge-green' : s.status === 'In Transit' ? 'badge-gold' : 'badge-red'}">
          ${s.status}
        </span>
      </td>
      <td>
        <button class="btn btn-outline btn-small" onclick="advanceSupplyStatus('${s.po}')">Update Status</button>
      </td>
    </tr>
  `).join('');
}

function advanceSupplyStatus(po) {
  const supplies = getStoredData(STORAGE_KEYS.SUPPLIES);
  const item = supplies.find(s => s.po === po);
  if (item) {
    if (item.status === 'Pending Approval') item.status = 'In Transit';
    else if (item.status === 'In Transit') item.status = 'Delivered';
    else item.status = 'Pending Approval';

    setStoredData(STORAGE_KEYS.SUPPLIES, supplies);
    renderSupplierDeliveries();
    showToast(`PO ${po} status changed to ${item.status}`, 'success');
  }
}

function promptNewSupplyOrder() {
  const supplier = prompt('Supplier Company Name (e.g. Karen Fishery Wholesalers):');
  if (!supplier) return;
  const item = prompt('Item Description (e.g. 150 KG Fresh Tilapia & Nile Perch):');
  if (!item) return;

  const supplies = getStoredData(STORAGE_KEYS.SUPPLIES);
  const newPO = 'PO-MV-2026-0' + (supplies.length + 85);
  supplies.unshift({
    po: newPO,
    supplier,
    item,
    qty: '150 KG',
    date: '2026-11-14',
    status: 'Pending Approval'
  });
  setStoredData(STORAGE_KEYS.SUPPLIES, supplies);
  renderSupplierDeliveries();
  showToast(`Supply requisition ${newPO} created!`, 'success');
}

function exportFinanceCSV() {
  const bookings = getStoredData(STORAGE_KEYS.BOOKINGS);
  let csvContent = 'data:text/csv;charset=utf-8,TicketCode,CustomerName,Phone,Date,Amount_KES,PaymentMethod,MpesaRef,Status\n';

  bookings.forEach(b => {
    csvContent += `"${b.ticketCode}","${b.customerName}","${b.customerPhone}","${b.visitDate}",${b.totalAmount},"${b.paymentMethod}","${b.mpesaRef}","${b.gateStatus}"\n`;
  });

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `Mamba_Village_Finance_Reconciliation_${new Date().toISOString().split('T')[0]}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  showToast('Finance CSV statement downloaded!', 'success');
}

function resetPrototypeData() {
  if (confirm('Reset prototype data to original KeMU demonstration baseline?')) {
    localStorage.removeItem(STORAGE_KEYS.BOOKINGS);
    localStorage.removeItem(STORAGE_KEYS.REVIEWS);
    localStorage.removeItem(STORAGE_KEYS.SUPPLIES);
    localStorage.removeItem(STORAGE_KEYS.GUIDES);
    localStorage.removeItem(STORAGE_KEYS.EVENTS);
    initializeDataStores();
    renderAttractions();
    renderScheduleTimeline('all');
    renderReviewsWall();
    renderAdminOverview();
    renderAdminBookingsTable();
    renderFinanceLedger();
    renderTourGuidesRoster();
    renderAdminEventsManager();
    renderSupplierDeliveries();
    showToast('Prototype data reset to initial proposal state.', 'success');
  }
}

// Gate Scanner & Validation Engine
function openGateScannerModal() {
  openModal('gateScannerModal');
  const resultBox = document.getElementById('gateValidationResult');
  if (resultBox) resultBox.style.display = 'none';
  const input = document.getElementById('manualTicketInput');
  if (input) input.value = '';
}

function verifyTicketFromGate() {
  const input = document.getElementById('manualTicketInput');
  const code = input ? input.value.trim().toUpperCase() : '';
  validateTicketCodeDirect(code);
}

function validateTicketCodeDirect(ticketCode) {
  if (!ticketCode) {
    showToast('Please enter a ticket code to scan.', 'warning');
    return;
  }

  const bookings = getStoredData(STORAGE_KEYS.BOOKINGS);
  const booking = bookings.find(b => b.ticketCode.toUpperCase() === ticketCode.toUpperCase());
  const resultBox = document.getElementById('gateValidationResult');

  if (!booking) {
    if (resultBox) {
      resultBox.style.display = 'block';
      resultBox.style.background = '#fee2e2';
      resultBox.style.color = '#991b1b';
      resultBox.innerHTML = `❌ INVALID TICKET CODE: "${ticketCode}". No booking record found in sanctuary database.`;
    }
    showToast('Validation Failed: Unrecognized ticket code.', 'error');
    return;
  }

  if (booking.gateStatus === 'Validated') {
    if (resultBox) {
      resultBox.style.display = 'block';
      resultBox.style.background = '#fef3c7';
      resultBox.style.color = '#92400e';
      resultBox.innerHTML = `⚠️ TICKET ALREADY REDEEMED: "${ticketCode}" was previously admitted on ${booking.visitDate}.`;
    }
    showToast('Warning: Ticket already used for entry.', 'warning');
    return;
  }

  // Mark as validated
  booking.gateStatus = 'Validated';
  setStoredData(STORAGE_KEYS.BOOKINGS, bookings);

  if (resultBox) {
    resultBox.style.display = 'block';
    resultBox.style.background = '#d1fae5';
    resultBox.style.color = '#065f46';
    resultBox.innerHTML = `✅ ACCESS GRANTED! Ticket ${ticketCode} for ${booking.customerName} validated successfully. Turnstile open.`;
  }

  showToast(`✅ Gate Entry Validated for ${booking.customerName}!`, 'success');
  renderAdminOverview();
  renderAdminBookingsTable();
}

// ============================================================================
// 7. UTILITIES & MODAL HELPERS
// ============================================================================

function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.style.display = 'flex';
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.style.display = 'none';
}

function showToast(message, type = 'success') {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `
    <span>${type === 'success' ? '✓' : type === 'warning' ? '⚠️' : '✕'}</span>
    <span>${message}</span>
  `;

  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}

function scrollToBooking() {
  switchMainView('visitor');
  const el = document.getElementById('booking');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}

function scrollToElement(id) {
  switchMainView('visitor');
  const el = document.getElementById(id);
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}

