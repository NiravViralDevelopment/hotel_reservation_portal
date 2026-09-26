/**
 * Static dummy data — UK Hotel Group Booking Management
 * Replace with API fetch calls when backend is connected.
 */

const HGBMS_DATA = {
  stats: {
    totalHotels: 14,
    totalCompanies: 8,
    totalAgencies: 24,
    activeGroups: 187,
    todayArrivals: 6,
    todayDepartures: 4,
    pendingEnquiries: 23,
    cancelledGroups: 12,
    revenue: 2847650,
    roomNights: 18420,
    averageAdr: 154.65,
    breakfastRevenue: 342180,
    dinnerRevenue: 518920,
    outstandingPayments: 89450
  },

  monthlyRevenue: [
    { month: 'Jan', value: 198400 },
    { month: 'Feb', value: 215600 },
    { month: 'Mar', value: 248900 },
    { month: 'Apr', value: 312400 },
    { month: 'May', value: 389200 },
    { month: 'Jun', value: 425800 },
    { month: 'Jul', value: 478200 },
    { month: 'Aug', value: 512600 },
    { month: 'Sep', value: 398400 },
    { month: 'Oct', value: 356200 },
    { month: 'Nov', value: 278900 },
    { month: 'Dec', value: 232050 }
  ],

  agencyShare: [
    { name: 'TUI UK', pct: 20, color: '#1e3a5f' },
    { name: 'Jet2holidays', pct: 16, color: '#0d9488' },
    { name: 'Riviera Travel', pct: 15, color: '#c9a227' },
    { name: 'Titan Travel', pct: 14, color: '#3b82f6' },
    { name: 'Leger Holidays', pct: 13, color: '#8b5cf6' },
    { name: 'Others', pct: 22, color: '#6b7280' }
  ],

  topHotels: [
    { name: 'Grand Brighton Hotel', city: 'Brighton', groups: 34, revenue: 428600, occupancy: 87 },
    { name: 'Edinburgh Castle View', city: 'Edinburgh', groups: 28, revenue: 392400, occupancy: 82 },
    { name: 'Lake District Manor', city: 'Windermere', groups: 26, revenue: 356800, occupancy: 79 },
    { name: 'Bath Royal Crescent', city: 'Bath', groups: 22, revenue: 298200, occupancy: 75 },
    { name: 'York Minster Inn', city: 'York', groups: 19, revenue: 245600, occupancy: 71 }
  ],

  recentActivity: [
    { time: '10 minutes ago', text: 'New group booking GRB-2026-0847 confirmed for TUI UK — Grand Brighton Hotel', type: 'success' },
    { time: '32 minutes ago', text: 'Payment received £24,850 from Jet2holidays for GRB-2026-0821', type: 'info' },
    { time: '1 hour ago', text: 'Enquiry ENQ-2026-0312 status changed to Quoted', type: 'warning' },
    { time: '2 hours ago', text: 'Booking GRB-2026-0799 cancelled by Riviera Travel — 42 rooms released', type: 'danger' },
    { time: '3 hours ago', text: 'Room allocation updated for GRB-2026-0835 at Lake District Manor', type: 'success' },
    { time: '4 hours ago', text: 'New enquiry received from Saga Holidays — 55 pax, September 2026', type: 'info' }
  ],

  upcomingArrivals: [
    { ref: 'GRB-2026-0847', group: 'Coastal Explorer Tour', hotel: 'Grand Brighton Hotel', agency: 'TUI UK', arrival: '27 Jun 2026', rooms: 48, pax: 92 },
    { ref: 'GRB-2026-0851', group: 'Highland Heritage', hotel: 'Edinburgh Castle View', agency: 'Riviera Travel', arrival: '27 Jun 2026', rooms: 32, pax: 58 },
    { ref: 'GRB-2026-0855', group: 'Cotswolds Discovery', hotel: 'Bath Royal Crescent', agency: 'Titan Travel', arrival: '28 Jun 2026', rooms: 28, pax: 52 },
    { ref: 'GRB-2026-0860', group: 'Scottish Whisky Trail', hotel: 'Edinburgh Castle View', agency: 'Leger Holidays', arrival: '29 Jun 2026', rooms: 40, pax: 76 },
    { ref: 'GRB-2026-0864', group: 'Lake District Ramblers', hotel: 'Lake District Manor', agency: 'Shearings', arrival: '30 Jun 2026', rooms: 36, pax: 68 }
  ],

  upcomingDepartures: [
    { ref: 'GRB-2026-0798', group: 'Yorkshire Dales Tour', hotel: 'York Minster Inn', agency: 'Newmarket Holidays', departure: '27 Jun 2026', rooms: 24, pax: 44 },
    { ref: 'GRB-2026-0802', group: 'Cornwall Coastline', hotel: 'Grand Brighton Hotel', agency: 'TUI UK', departure: '27 Jun 2026', rooms: 36, pax: 68 },
    { ref: 'GRB-2026-0808', group: 'Peak District Walk', hotel: 'Lake District Manor', agency: 'Jet2holidays', departure: '28 Jun 2026', rooms: 30, pax: 56 },
    { ref: 'GRB-2026-0814', group: 'Roman Britain Tour', hotel: 'Bath Royal Crescent', agency: 'Riviera Travel', departure: '29 Jun 2026', rooms: 22, pax: 40 }
  ],

  latestEnquiries: [
    { ref: 'ENQ-2026-0318', company: 'Saga Holidays', contact: 'Helen Marsh', subject: 'Autumn Colours Tour — 65 pax', status: 'New', date: '27 Jun 2026' },
    { ref: 'ENQ-2026-0315', company: 'TUI UK', contact: 'James Whitfield', subject: 'Christmas Market Break — Edinburgh', status: 'Follow Up', date: '26 Jun 2026' },
    { ref: 'ENQ-2026-0312', company: 'Titan Travel', contact: 'Sarah Connolly', subject: 'Garden Tour — Cotswolds May 2027', status: 'Quoted', date: '26 Jun 2026' },
    { ref: 'ENQ-2026-0308', company: 'Leger Holidays', contact: 'David Pemberton', subject: 'D-Day Anniversary Tour — 80 rooms', status: 'Confirmed', date: '25 Jun 2026' }
  ],

  notifications: [
    { title: 'New Booking Confirmed', text: 'GRB-2026-0847 — Coastal Explorer Tour, 48 rooms', time: '10 min ago', unread: true },
    { title: 'Payment Received', text: '£24,850 from Jet2holidays', time: '32 min ago', unread: true },
    { title: 'Enquiry Assigned', text: 'ENQ-2026-0318 assigned to you', time: '1 hr ago', unread: true },
    { title: 'Cancellation Alert', text: 'GRB-2026-0799 cancelled — review inventory', time: '2 hrs ago', unread: false },
    { title: 'Document Uploaded', text: 'Rooming list for GRB-2026-0835', time: '4 hrs ago', unread: false }
  ],

  hotels: [
    { code: 'GBH01', name: 'Grand Brighton Hotel', city: 'Brighton', country: 'United Kingdom', rooms: 186, status: 'Active', manager: 'Sarah Mitchell', phone: '+44 1273 224300', email: 'sarah.mitchell@grandbrighton.co.uk' },
    { code: 'ECV01', name: 'Edinburgh Castle View', city: 'Edinburgh', country: 'United Kingdom', rooms: 142, status: 'Active', manager: 'Andrew Fraser', phone: '+44 131 556 7890', email: 'a.fraser@edinburghcastleview.co.uk' },
    { code: 'LDM01', name: 'Lake District Manor', city: 'Windermere', country: 'United Kingdom', rooms: 98, status: 'Active', manager: 'Emma Richardson', phone: '+44 15394 45678', email: 'e.richardson@lakemanor.co.uk' },
    { code: 'BRC01', name: 'Bath Royal Crescent', city: 'Bath', country: 'United Kingdom', rooms: 76, status: 'Active', manager: 'James Holloway', phone: '+44 1225 463000', email: 'j.holloway@bathcrescent.co.uk' },
    { code: 'YMI01', name: 'York Minster Inn', city: 'York', country: 'United Kingdom', rooms: 84, status: 'Active', manager: 'Claire Bennett', phone: '+44 1904 621000', email: 'c.bennett@yorkminsterinn.co.uk' },
    { code: 'CWH01', name: 'Cornwall Cliffside Hotel', city: 'St Ives', country: 'United Kingdom', rooms: 64, status: 'Active', manager: 'Mark Penrose', phone: '+44 1736 796000', email: 'm.penrose@cornwallcliff.co.uk' },
    { code: 'OBH01', name: 'Oxford Bodleian House', city: 'Oxford', country: 'United Kingdom', rooms: 52, status: 'Active', manager: 'Lucy Ashford', phone: '+44 1865 789000', email: 'l.ashford@oxfordbodleian.co.uk' },
    { code: 'CNH01', name: 'Canterbury Cathedral Hotel', city: 'Canterbury', country: 'United Kingdom', rooms: 68, status: 'Active', manager: 'Peter Watkins', phone: '+44 1227 765000', email: 'p.watkins@canterburycathedral.co.uk' },
    { code: 'STH01', name: 'Stratford Shakespeare Hotel', city: 'Stratford-upon-Avon', country: 'United Kingdom', rooms: 72, status: 'Active', manager: 'Helen Marsh', phone: '+44 1789 298000', email: 'h.marsh@stratfordshakespeare.co.uk' },
    { code: 'CNW01', name: 'Chester City Walls Hotel', city: 'Chester', country: 'United Kingdom', rooms: 58, status: 'Active', manager: 'David Pemberton', phone: '+44 1244 567000', email: 'd.pemberton@chestercitywalls.co.uk' },
    { code: 'HWH01', name: 'Harrogate Wellness Hotel', city: 'Harrogate', country: 'United Kingdom', rooms: 88, status: 'Active', manager: 'Rachel Green', phone: '+44 1423 567890', email: 'r.green@harrogatewellness.co.uk' },
    { code: 'NCH01', name: 'Norwich Cathedral Hotel', city: 'Norwich', country: 'United Kingdom', rooms: 54, status: 'Inactive', manager: 'Tom Bradley', phone: '+44 1603 456000', email: 't.bradley@norwichcathedral.co.uk' },
    { code: 'DVR01', name: 'Devon Riviera Resort', city: 'Torquay', country: 'United Kingdom', rooms: 112, status: 'Active', manager: 'Sophie Clarke', phone: '+44 1803 298000', email: 's.clarke@devonriviera.co.uk' },
    { code: 'LGH01', name: 'Lincoln Gothic Hotel', city: 'Lincoln', country: 'United Kingdom', rooms: 46, status: 'Active', manager: 'Michael Dunn', phone: '+44 1522 567000', email: 'm.dunn@lincolngothic.co.uk' }
  ],

  companies: [
    { id: 'CO001', name: 'Heritage Hotel Group Ltd', regNumber: '08472931', city: 'London', country: 'United Kingdom', hotels: 14, contacts: 28, revenue: 2847650, status: 'Active' },
    { id: 'CO002', name: 'Coastal Properties UK Ltd', regNumber: '09234567', city: 'Brighton', country: 'United Kingdom', hotels: 3, contacts: 8, revenue: 856400, status: 'Active' },
    { id: 'CO003', name: 'Scottish Hospitality Holdings', regNumber: 'SC456789', city: 'Edinburgh', country: 'United Kingdom', hotels: 2, contacts: 6, revenue: 624800, status: 'Active' },
    { id: 'CO004', name: 'Cotswolds Leisure Ltd', regNumber: '07891234', city: 'Cheltenham', country: 'United Kingdom', hotels: 2, contacts: 5, revenue: 412300, status: 'Active' },
    { id: 'CO005', name: 'Northern England Hotels PLC', regNumber: '04567890', city: 'York', country: 'United Kingdom', hotels: 3, contacts: 7, revenue: 534200, status: 'Active' },
    { id: 'CO006', name: 'West Country Resorts Ltd', regNumber: '03456789', city: 'Exeter', country: 'United Kingdom', hotels: 2, contacts: 4, revenue: 389600, status: 'Active' },
    { id: 'CO007', name: 'Midlands Heritage Hotels', regNumber: '02345678', city: 'Birmingham', country: 'United Kingdom', hotels: 1, contacts: 3, revenue: 198400, status: 'Active' },
    { id: 'CO008', name: 'East Anglia Hospitality Ltd', regNumber: '01234567', city: 'Norwich', country: 'United Kingdom', hotels: 1, contacts: 2, revenue: 124800, status: 'Inactive' }
  ],

  agencies: [
    { name: 'TUI UK', code: 'TUI', contact: 'James Whitfield', email: 'j.whitfield@tui.co.uk', phone: '+44 1733 419999', city: 'Luton', bookings: 48, revenue: 569530, status: 'Active' },
    { name: 'Jet2holidays', code: 'JET2', contact: 'Michelle Turner', email: 'm.turner@jet2holidays.com', phone: '+44 113 496 0000', city: 'Leeds', bookings: 36, revenue: 412800, status: 'Active' },
    { name: 'Riviera Travel', code: 'RIV', contact: 'Sarah Connolly', email: 's.connolly@rivieratravel.co.uk', phone: '+44 1582 798 000', city: 'Burton-on-Trent', bookings: 32, revenue: 398600, status: 'Active' },
    { name: 'Titan Travel', code: 'TTN', contact: 'Robert Hughes', email: 'r.hughes@titantravel.co.uk', phone: '+44 800 988 5823', city: 'Redhill', bookings: 28, revenue: 356400, status: 'Active' },
    { name: 'Leger Holidays', code: 'LEG', contact: 'David Pemberton', email: 'd.pemberton@leger.co.uk', phone: '+44 1709 787 000', city: 'Rotherham', bookings: 26, revenue: 324800, status: 'Active' },
    { name: 'Shearings', code: 'SHR', contact: 'Patricia Moore', email: 'p.moore@shearings.com', phone: '+44 1704 567 000', city: 'Chorley', bookings: 22, revenue: 278400, status: 'Active' },
    { name: 'Newmarket Holidays', code: 'NMK', contact: 'Alan Fisher', email: 'a.fisher@newmarketholidays.co.uk', phone: '+44 208 241 0000', city: 'London', bookings: 20, revenue: 245600, status: 'Active' },
    { name: 'Saga Holidays', code: 'SAG', contact: 'Helen Marsh', email: 'h.marsh@saga.co.uk', phone: '+44 808 252 0000', city: 'Folkestone', bookings: 18, revenue: 218400, status: 'Active' }
  ],

  contacts: [
    { company: 'Heritage Hotel Group Ltd', agency: 'TUI UK', name: 'James Whitfield', email: 'j.whitfield@tui.co.uk', phone: '+44 1733 419999', position: 'Group Sales Manager', country: 'United Kingdom', notes: 'Primary contact for summer programmes' },
    { company: 'Heritage Hotel Group Ltd', agency: 'Jet2holidays', name: 'Michelle Turner', email: 'm.turner@jet2holidays.com', phone: '+44 113 496 0000', position: 'Contract Manager', country: 'United Kingdom', notes: 'Handles Yorkshire & Lake District routes' },
    { company: 'Coastal Properties UK Ltd', agency: 'Riviera Travel', name: 'Sarah Connolly', email: 's.connolly@rivieratravel.co.uk', phone: '+44 1582 798 000', position: 'Senior Account Manager', country: 'United Kingdom', notes: 'Escorted tours specialist' },
    { company: 'Scottish Hospitality Holdings', agency: 'Titan Travel', name: 'Robert Hughes', email: 'r.hughes@titantravel.co.uk', phone: '+44 800 988 5823', position: 'Product Manager', country: 'United Kingdom', notes: 'Luxury coach tour programmes' },
    { company: 'Northern England Hotels PLC', agency: 'Leger Holidays', name: 'David Pemberton', email: 'd.pemberton@leger.co.uk', phone: '+44 1709 787 000', position: 'Groups Coordinator', country: 'United Kingdom', notes: 'D-Day & heritage tours' },
    { company: 'Heritage Hotel Group Ltd', agency: 'Shearings', name: 'Patricia Moore', email: 'p.moore@shearings.com', phone: '+44 1704 567 000', position: 'Allocation Manager', country: 'United Kingdom', notes: 'Rooming list deadlines strict' },
    { company: 'West Country Resorts Ltd', agency: 'Newmarket Holidays', name: 'Alan Fisher', email: 'a.fisher@newmarketholidays.co.uk', phone: '+44 208 241 0000', position: 'Sales Director', country: 'United Kingdom', notes: 'Cornwall & Devon programmes' },
    { company: 'Heritage Hotel Group Ltd', agency: 'Saga Holidays', name: 'Helen Marsh', email: 'h.marsh@saga.co.uk', phone: '+44 808 252 0000', position: 'Over 50s Programme Lead', country: 'United Kingdom', notes: 'Autumn colours & garden tours' },
    { company: 'Coastal Properties UK Ltd', agency: 'TUI UK', name: 'Karen Stevens', email: 'k.stevens@tui.co.uk', phone: '+44 1733 419888', position: 'Operations Assistant', country: 'United Kingdom', notes: 'Backup contact' },
    { company: 'Cotswolds Leisure Ltd', agency: 'Riviera Travel', name: 'Emma Richardson', email: 'e.richardson@rivieratravel.co.uk', phone: '+44 1582 798 100', position: 'Contract Administrator', country: 'United Kingdom', notes: 'Cotswolds & Bath routes' }
  ],

  bookings: [
    { ref: 'GRB-2026-0847', group: 'Coastal Explorer Tour', company: 'Coastal Properties UK Ltd', agency: 'TUI UK', hotel: 'Grand Brighton Hotel', arrival: '27 Jun 2026', departure: '30 Jun 2026', nights: 3, rooms: 48, pax: 92, mealPlan: 'HB', breakfast: true, dinner: true, adr: 158.00, revenue: 22752, paymentStatus: 'Paid', bookingStatus: 'Confirmed', created: '15 Mar 2026' },
    { ref: 'GRB-2026-0851', group: 'Highland Heritage', company: 'Scottish Hospitality Holdings', agency: 'Riviera Travel', hotel: 'Edinburgh Castle View', arrival: '27 Jun 2026', departure: '29 Jun 2026', nights: 2, rooms: 32, pax: 58, mealPlan: 'BB', breakfast: true, dinner: false, adr: 142.50, revenue: 9120, paymentStatus: 'Paid', bookingStatus: 'Confirmed', created: '18 Mar 2026' },
    { ref: 'GRB-2026-0855', group: 'Cotswolds Discovery', company: 'Cotswolds Leisure Ltd', agency: 'Titan Travel', hotel: 'Bath Royal Crescent', arrival: '28 Jun 2026', departure: '01 Jul 2026', nights: 3, rooms: 28, pax: 52, mealPlan: 'HB', breakfast: true, dinner: true, adr: 165.00, revenue: 13860, paymentStatus: 'Partial', bookingStatus: 'Confirmed', created: '20 Mar 2026' },
    { ref: 'GRB-2026-0860', group: 'Scottish Whisky Trail', company: 'Scottish Hospitality Holdings', agency: 'Leger Holidays', hotel: 'Edinburgh Castle View', arrival: '29 Jun 2026', departure: '02 Jul 2026', nights: 3, rooms: 40, pax: 76, mealPlan: 'FB', breakfast: true, dinner: true, adr: 172.00, revenue: 20640, paymentStatus: 'Paid', bookingStatus: 'Confirmed', created: '22 Mar 2026' },
    { ref: 'GRB-2026-0864', group: 'Lake District Ramblers', company: 'Northern England Hotels PLC', agency: 'Shearings', hotel: 'Lake District Manor', arrival: '30 Jun 2026', departure: '03 Jul 2026', nights: 3, rooms: 36, pax: 68, mealPlan: 'HB', breakfast: true, dinner: true, adr: 148.00, revenue: 15984, paymentStatus: 'Unpaid', bookingStatus: 'Provisional', created: '25 Mar 2026' },
    { ref: 'GRB-2026-0821', group: 'Yorkshire Dales Explorer', company: 'Northern England Hotels PLC', agency: 'Jet2holidays', hotel: 'York Minster Inn', arrival: '05 Jul 2026', departure: '08 Jul 2026', nights: 3, rooms: 30, pax: 56, mealPlan: 'BB', breakfast: true, dinner: false, adr: 135.00, revenue: 12150, paymentStatus: 'Paid', bookingStatus: 'Confirmed', created: '10 Feb 2026' },
    { ref: 'GRB-2026-0835', group: 'Peak District Walk', company: 'Northern England Hotels PLC', agency: 'Jet2holidays', hotel: 'Lake District Manor', arrival: '08 Jul 2026', departure: '11 Jul 2026', nights: 3, rooms: 30, pax: 56, mealPlan: 'HB', breakfast: true, dinner: true, adr: 152.00, revenue: 13680, paymentStatus: 'Paid', bookingStatus: 'Confirmed', created: '28 Feb 2026' },
    { ref: 'GRB-2026-0799', group: 'Roman Britain Tour', company: 'Cotswolds Leisure Ltd', agency: 'Riviera Travel', hotel: 'Bath Royal Crescent', arrival: '12 Jul 2026', departure: '15 Jul 2026', nights: 3, rooms: 42, pax: 78, mealPlan: 'HB', breakfast: true, dinner: true, adr: 160.00, revenue: 20160, paymentStatus: 'Partial', bookingStatus: 'Cancelled', created: '05 Jan 2026' },
    { ref: 'GRB-2026-0870', group: 'Cornwall Coastline', company: 'West Country Resorts Ltd', agency: 'TUI UK', hotel: 'Cornwall Cliffside Hotel', arrival: '15 Jul 2026', departure: '18 Jul 2026', nights: 3, rooms: 36, pax: 68, mealPlan: 'HB', breakfast: true, dinner: true, adr: 155.00, revenue: 16740, paymentStatus: 'Paid', bookingStatus: 'Confirmed', created: '01 Apr 2026' },
    { ref: 'GRB-2026-0875', group: 'Oxford Literary Tour', company: 'Midlands Heritage Hotels', agency: 'Newmarket Holidays', hotel: 'Oxford Bodleian House', arrival: '18 Jul 2026', departure: '20 Jul 2026', nights: 2, rooms: 24, pax: 44, mealPlan: 'BB', breakfast: true, dinner: false, adr: 128.00, revenue: 6144, paymentStatus: 'Paid', bookingStatus: 'Confirmed', created: '05 Apr 2026' },
    { ref: 'GRB-2026-0880', group: 'Canterbury Pilgrimage', company: 'Heritage Hotel Group Ltd', agency: 'Saga Holidays', hotel: 'Canterbury Cathedral Hotel', arrival: '22 Jul 2026', departure: '25 Jul 2026', nights: 3, rooms: 28, pax: 52, mealPlan: 'HB', breakfast: true, dinner: true, adr: 145.00, revenue: 12180, paymentStatus: 'Unpaid', bookingStatus: 'Pending', created: '08 Apr 2026' },
    { ref: 'GRB-2026-0885', group: 'Shakespeare Country', company: 'Midlands Heritage Hotels', agency: 'Titan Travel', hotel: 'Stratford Shakespeare Hotel', arrival: '25 Jul 2026', departure: '28 Jul 2026', nights: 3, rooms: 32, pax: 60, mealPlan: 'HB', breakfast: true, dinner: true, adr: 150.00, revenue: 14400, paymentStatus: 'Partial', bookingStatus: 'Confirmed', created: '12 Apr 2026' },
    { ref: 'GRB-2026-0890', group: 'Chester Heritage Walk', company: 'Northern England Hotels PLC', agency: 'Leger Holidays', hotel: 'Chester City Walls Hotel', arrival: '01 Aug 2026', departure: '04 Aug 2026', nights: 3, rooms: 26, pax: 48, mealPlan: 'BB', breakfast: true, dinner: false, adr: 138.00, revenue: 10764, paymentStatus: 'Paid', bookingStatus: 'Confirmed', created: '15 Apr 2026' },
    { ref: 'GRB-2026-0895', group: 'Harrogate Spa Break', company: 'Northern England Hotels PLC', agency: 'Shearings', hotel: 'Harrogate Wellness Hotel', arrival: '05 Aug 2026', departure: '08 Aug 2026', nights: 3, rooms: 40, pax: 74, mealPlan: 'FB', breakfast: true, dinner: true, adr: 168.00, revenue: 20160, paymentStatus: 'Paid', bookingStatus: 'Confirmed', created: '18 Apr 2026' },
    { ref: 'GRB-2026-0900', group: 'Devon Riviera Escape', company: 'West Country Resorts Ltd', agency: 'TUI UK', hotel: 'Devon Riviera Resort', arrival: '10 Aug 2026', departure: '14 Aug 2026', nights: 4, rooms: 52, pax: 98, mealPlan: 'HB', breakfast: true, dinner: true, adr: 162.00, revenue: 33696, paymentStatus: 'Partial', bookingStatus: 'Confirmed', created: '22 Apr 2026' },
    { ref: 'GRB-2026-0905', group: 'Lincoln Cathedral Tour', company: 'East Anglia Hospitality Ltd', agency: 'Newmarket Holidays', hotel: 'Lincoln Gothic Hotel', arrival: '15 Aug 2026', departure: '17 Aug 2026', nights: 2, rooms: 22, pax: 40, mealPlan: 'BB', breakfast: true, dinner: false, adr: 125.00, revenue: 5500, paymentStatus: 'Unpaid', bookingStatus: 'Provisional', created: '25 Apr 2026' },
    { ref: 'GRB-2026-0910', group: 'Autumn Colours North', company: 'Scottish Hospitality Holdings', agency: 'Saga Holidays', hotel: 'Edinburgh Castle View', arrival: '20 Sep 2026', departure: '24 Sep 2026', nights: 4, rooms: 44, pax: 82, mealPlan: 'HB', breakfast: true, dinner: true, adr: 175.00, revenue: 30800, paymentStatus: 'Paid', bookingStatus: 'Confirmed', created: '01 May 2026' },
    { ref: 'GRB-2026-0915', group: 'Christmas Market Edinburgh', company: 'Scottish Hospitality Holdings', agency: 'TUI UK', hotel: 'Edinburgh Castle View', arrival: '05 Dec 2026', departure: '08 Dec 2026', nights: 3, rooms: 38, pax: 72, mealPlan: 'HB', breakfast: true, dinner: true, adr: 185.00, revenue: 21090, paymentStatus: 'Unpaid', bookingStatus: 'Pending', created: '10 May 2026' },
    { ref: 'GRB-2026-0920', group: 'Garden Tour Cotswolds', company: 'Cotswolds Leisure Ltd', agency: 'Titan Travel', hotel: 'Bath Royal Crescent', arrival: '12 May 2027', departure: '15 May 2027', nights: 3, rooms: 30, pax: 56, mealPlan: 'HB', breakfast: true, dinner: true, adr: 170.00, revenue: 15300, paymentStatus: 'Partial', bookingStatus: 'Confirmed', created: '15 May 2026' },
    { ref: 'GRB-2026-0925', group: 'D-Day Anniversary Tour', company: 'Heritage Hotel Group Ltd', agency: 'Leger Holidays', hotel: 'Grand Brighton Hotel', arrival: '06 Jun 2027', departure: '10 Jun 2027', nights: 4, rooms: 80, pax: 152, mealPlan: 'FB', breakfast: true, dinner: true, adr: 195.00, revenue: 62400, paymentStatus: 'Partial', bookingStatus: 'Confirmed', created: '20 May 2026' }
  ],

  enquiries: [
    { ref: 'ENQ-2026-0318', agency: 'Saga Holidays', contact: 'Helen Marsh', subject: 'Autumn Colours Tour — 65 pax', hotel: 'Edinburgh Castle View', arrival: 'Oct 2026', pax: 65, status: 'New', assigned: 'Richard Whitmore', date: '27 Jun 2026' },
    { ref: 'ENQ-2026-0315', agency: 'TUI UK', contact: 'James Whitfield', subject: 'Christmas Market Break — Edinburgh', hotel: 'Edinburgh Castle View', arrival: 'Dec 2026', pax: 72, status: 'Follow Up', assigned: 'Sarah Mitchell', date: '26 Jun 2026' },
    { ref: 'ENQ-2026-0312', agency: 'Titan Travel', contact: 'Sarah Connolly', subject: 'Garden Tour — Cotswolds May 2027', hotel: 'Bath Royal Crescent', arrival: 'May 2027', pax: 56, status: 'Quoted', assigned: 'Richard Whitmore', date: '26 Jun 2026' },
    { ref: 'ENQ-2026-0308', agency: 'Leger Holidays', contact: 'David Pemberton', subject: 'D-Day Anniversary Tour — 80 rooms', hotel: 'Grand Brighton Hotel', arrival: 'Jun 2027', pax: 152, status: 'Confirmed', assigned: 'Richard Whitmore', date: '25 Jun 2026' },
    { ref: 'ENQ-2026-0305', agency: 'Jet2holidays', contact: 'Michelle Turner', subject: 'Lake District Summer Programme', hotel: 'Lake District Manor', arrival: 'Jul 2026', pax: 60, status: 'Quoted', assigned: 'Emma Richardson', date: '24 Jun 2026' },
    { ref: 'ENQ-2026-0301', agency: 'Riviera Travel', contact: 'Sarah Connolly', subject: 'Roman Baths Extended Stay', hotel: 'Bath Royal Crescent', arrival: 'Sep 2026', pax: 45, status: 'Lost', assigned: 'James Holloway', date: '22 Jun 2026' },
    { ref: 'ENQ-2026-0298', agency: 'Shearings', contact: 'Patricia Moore', subject: 'York Minster Choir Tour', hotel: 'York Minster Inn', arrival: 'Aug 2026', pax: 38, status: 'Cancelled', assigned: 'Claire Bennett', date: '20 Jun 2026' },
    { ref: 'ENQ-2026-0295', agency: 'Newmarket Holidays', contact: 'Alan Fisher', subject: 'Cornwall Coastal Route 2027', hotel: 'Cornwall Cliffside Hotel', arrival: 'Apr 2027', pax: 55, status: 'New', assigned: 'Mark Penrose', date: '18 Jun 2026' }
  ],

  cancelledBookings: [
    { ref: 'GRB-2026-0799', group: 'Roman Britain Tour', agency: 'Riviera Travel', hotel: 'Bath Royal Crescent', arrival: '12 Jul 2026', cancelledDate: '24 Jun 2026', reason: 'Insufficient uptake — tour cancelled by operator', revenueLost: 20160 },
    { ref: 'GRB-2026-0756', group: 'Norfolk Broads Cruise', agency: 'Shearings', hotel: 'Norwich Cathedral Hotel', arrival: '08 Aug 2026', cancelledDate: '15 Jun 2026', reason: 'Hotel refurbishment delay', revenueLost: 8640 },
    { ref: 'GRB-2026-0720', group: 'Welsh Borders Tour', agency: 'Leger Holidays', hotel: 'Chester City Walls Hotel', arrival: '14 Sep 2026', cancelledDate: '02 Jun 2026', reason: 'Client requested alternative dates — rebooked elsewhere', revenueLost: 11200 },
    { ref: 'GRB-2026-0688', group: 'Spring Gardens Tour', agency: 'Titan Travel', hotel: 'Harrogate Wellness Hotel', arrival: '20 Apr 2026', cancelledDate: '18 May 2026', reason: 'Force majeure — coach operator strike', revenueLost: 15680 },
    { ref: 'GRB-2026-0650', group: 'Scottish Islands Extension', agency: 'Riviera Travel', hotel: 'Edinburgh Castle View', arrival: '03 Oct 2026', cancelledDate: '10 May 2026', reason: 'Ferry service discontinued for season', revenueLost: 18900 }
  ],

  arrivals: {
    today: [
      { ref: 'GRB-2026-0847', group: 'Coastal Explorer Tour', hotel: 'Grand Brighton Hotel', agency: 'TUI UK', arrival: '27 Jun 2026', rooms: 48, pax: 92, status: 'Confirmed' },
      { ref: 'GRB-2026-0851', group: 'Highland Heritage', hotel: 'Edinburgh Castle View', agency: 'Riviera Travel', arrival: '27 Jun 2026', rooms: 32, pax: 58, status: 'Confirmed' },
      { ref: 'GRB-2026-0798', group: 'Yorkshire Dales Tour', hotel: 'York Minster Inn', agency: 'Newmarket Holidays', arrival: '27 Jun 2026', rooms: 24, pax: 44, status: 'Confirmed' },
      { ref: 'GRB-2026-0802', group: 'Brighton Weekend Break', hotel: 'Grand Brighton Hotel', agency: 'TUI UK', arrival: '27 Jun 2026', rooms: 18, pax: 34, status: 'Confirmed' },
      { ref: 'GRB-2026-0805', group: 'Edinburgh City Break', hotel: 'Edinburgh Castle View', agency: 'Jet2holidays', arrival: '27 Jun 2026', rooms: 22, pax: 40, status: 'Provisional' },
      { ref: 'GRB-2026-0809', group: 'Lake District Day Trip', hotel: 'Lake District Manor', agency: 'Shearings', arrival: '27 Jun 2026', rooms: 15, pax: 28, status: 'Confirmed' }
    ],
    tomorrow: [
      { ref: 'GRB-2026-0855', group: 'Cotswolds Discovery', hotel: 'Bath Royal Crescent', agency: 'Titan Travel', arrival: '28 Jun 2026', rooms: 28, pax: 52, status: 'Confirmed' },
      { ref: 'GRB-2026-0812', group: 'Bath Spa Experience', hotel: 'Bath Royal Crescent', agency: 'Riviera Travel', arrival: '28 Jun 2026', rooms: 20, pax: 36, status: 'Confirmed' },
      { ref: 'GRB-2026-0816', group: 'Windermere Walkers', hotel: 'Lake District Manor', agency: 'Jet2holidays', arrival: '28 Jun 2026', rooms: 25, pax: 46, status: 'Confirmed' },
      { ref: 'GRB-2026-0820', group: 'York History Tour', hotel: 'York Minster Inn', agency: 'Leger Holidays', arrival: '28 Jun 2026', rooms: 30, pax: 54, status: 'Confirmed' }
    ],
    week: [
      { ref: 'GRB-2026-0860', group: 'Scottish Whisky Trail', hotel: 'Edinburgh Castle View', agency: 'Leger Holidays', arrival: '29 Jun 2026', rooms: 40, pax: 76, status: 'Confirmed' },
      { ref: 'GRB-2026-0864', group: 'Lake District Ramblers', hotel: 'Lake District Manor', agency: 'Shearings', arrival: '30 Jun 2026', rooms: 36, pax: 68, status: 'Provisional' },
      { ref: 'GRB-2026-0821', group: 'Yorkshire Dales Explorer', hotel: 'York Minster Inn', agency: 'Jet2holidays', arrival: '05 Jul 2026', rooms: 30, pax: 56, status: 'Confirmed' },
      { ref: 'GRB-2026-0835', group: 'Peak District Walk', hotel: 'Lake District Manor', agency: 'Jet2holidays', arrival: '08 Jul 2026', rooms: 30, pax: 56, status: 'Confirmed' },
      { ref: 'GRB-2026-0870', group: 'Cornwall Coastline', hotel: 'Cornwall Cliffside Hotel', agency: 'TUI UK', arrival: '15 Jul 2026', rooms: 36, pax: 68, status: 'Confirmed' },
      { ref: 'GRB-2026-0875', group: 'Oxford Literary Tour', hotel: 'Oxford Bodleian House', agency: 'Newmarket Holidays', arrival: '18 Jul 2026', rooms: 24, pax: 44, status: 'Confirmed' },
      { ref: 'GRB-2026-0880', group: 'Canterbury Pilgrimage', hotel: 'Canterbury Cathedral Hotel', agency: 'Saga Holidays', arrival: '22 Jul 2026', rooms: 28, pax: 52, status: 'Pending' },
      { ref: 'GRB-2026-0885', group: 'Shakespeare Country', hotel: 'Stratford Shakespeare Hotel', agency: 'Titan Travel', arrival: '25 Jul 2026', rooms: 32, pax: 60, status: 'Confirmed' }
    ]
  },

  departures: {
    today: [
      { ref: 'GRB-2026-0798', group: 'Yorkshire Dales Tour', hotel: 'York Minster Inn', agency: 'Newmarket Holidays', departure: '27 Jun 2026', rooms: 24, pax: 44, status: 'Confirmed' },
      { ref: 'GRB-2026-0802', group: 'Cornwall Coastline', hotel: 'Grand Brighton Hotel', agency: 'TUI UK', departure: '27 Jun 2026', rooms: 36, pax: 68, status: 'Confirmed' },
      { ref: 'GRB-2026-0790', group: 'Bath Heritage Stay', hotel: 'Bath Royal Crescent', agency: 'Riviera Travel', departure: '27 Jun 2026', rooms: 20, pax: 38, status: 'Confirmed' },
      { ref: 'GRB-2026-0785', group: 'Edinburgh Festival Prep', hotel: 'Edinburgh Castle View', agency: 'Titan Travel', departure: '27 Jun 2026', rooms: 28, pax: 50, status: 'Confirmed' }
    ],
    upcoming: [
      { ref: 'GRB-2026-0808', group: 'Peak District Walk', hotel: 'Lake District Manor', agency: 'Jet2holidays', departure: '28 Jun 2026', rooms: 30, pax: 56, status: 'Confirmed' },
      { ref: 'GRB-2026-0814', group: 'Roman Britain Tour', hotel: 'Bath Royal Crescent', agency: 'Riviera Travel', departure: '29 Jun 2026', rooms: 22, pax: 40, status: 'Confirmed' },
      { ref: 'GRB-2026-0847', group: 'Coastal Explorer Tour', hotel: 'Grand Brighton Hotel', agency: 'TUI UK', departure: '30 Jun 2026', rooms: 48, pax: 92, status: 'Confirmed' },
      { ref: 'GRB-2026-0851', group: 'Highland Heritage', hotel: 'Edinburgh Castle View', agency: 'Riviera Travel', departure: '29 Jun 2026', rooms: 32, pax: 58, status: 'Confirmed' },
      { ref: 'GRB-2026-0855', group: 'Cotswolds Discovery', hotel: 'Bath Royal Crescent', agency: 'Titan Travel', departure: '01 Jul 2026', rooms: 28, pax: 52, status: 'Confirmed' },
      { ref: 'GRB-2026-0860', group: 'Scottish Whisky Trail', hotel: 'Edinburgh Castle View', agency: 'Leger Holidays', departure: '02 Jul 2026', rooms: 40, pax: 76, status: 'Confirmed' }
    ]
  },

  users: [
    { initials: 'RW', name: 'Richard Whitmore', email: 'r.whitmore@hotelgroup.co.uk', role: 'Group Reservations Manager', department: 'Reservations', lastLogin: '27 Jun 2026, 08:42', status: 'Active' },
    { initials: 'SM', name: 'Sarah Mitchell', email: 's.mitchell@grandbrighton.co.uk', role: 'Hotel Manager', department: 'Operations', lastLogin: '27 Jun 2026, 07:15', status: 'Active' },
    { initials: 'AF', name: 'Andrew Fraser', email: 'a.fraser@edinburghcastleview.co.uk', role: 'Hotel Manager', department: 'Operations', lastLogin: '26 Jun 2026, 18:30', status: 'Active' },
    { initials: 'ER', name: 'Emma Richardson', email: 'e.richardson@lakemanor.co.uk', role: 'Reservations Coordinator', department: 'Reservations', lastLogin: '27 Jun 2026, 09:01', status: 'Active' },
    { initials: 'JH', name: 'James Holloway', email: 'j.holloway@bathcrescent.co.uk', role: 'Hotel Manager', department: 'Operations', lastLogin: '25 Jun 2026, 16:45', status: 'Active' },
    { initials: 'CB', name: 'Claire Bennett', email: 'c.bennett@yorkminsterinn.co.uk', role: 'Reservations Coordinator', department: 'Reservations', lastLogin: '27 Jun 2026, 08:20', status: 'Active' },
    { initials: 'LG', name: 'Laura Green', email: 'l.green@hotelgroup.co.uk', role: 'Finance Manager', department: 'Finance', lastLogin: '26 Jun 2026, 17:00', status: 'Active' },
    { initials: 'TB', name: 'Tom Bradley', email: 't.bradley@hotelgroup.co.uk', role: 'System Administrator', department: 'IT', lastLogin: '24 Jun 2026, 11:22', status: 'Inactive' }
  ],

  auditLogs: [
    { timestamp: '27 Jun 2026, 10:15', user: 'Richard Whitmore', action: 'Created', module: 'Group Bookings', details: 'GRB-2026-0847 confirmed', ip: '192.168.1.45' },
    { timestamp: '27 Jun 2026, 09:48', user: 'Emma Richardson', action: 'Updated', module: 'Enquiries', details: 'ENQ-2026-0312 status → Quoted', ip: '192.168.1.52' },
    { timestamp: '27 Jun 2026, 09:22', user: 'Laura Green', action: 'Payment', module: 'Finance', details: '£24,850 received — Jet2holidays', ip: '192.168.1.38' },
    { timestamp: '27 Jun 2026, 08:55', user: 'Richard Whitmore', action: 'Cancelled', module: 'Group Bookings', details: 'GRB-2026-0799 cancelled', ip: '192.168.1.45' },
    { timestamp: '26 Jun 2026, 17:30', user: 'Sarah Mitchell', action: 'Uploaded', module: 'Documents', details: 'Rooming list GRB-2026-0835.pdf', ip: '192.168.2.12' },
    { timestamp: '26 Jun 2026, 16:12', user: 'Andrew Fraser', action: 'Updated', module: 'Hotels', details: 'ECV01 room allocation amended', ip: '192.168.2.08' },
    { timestamp: '26 Jun 2026, 14:45', user: 'Claire Bennett', action: 'Created', module: 'Enquiries', details: 'ENQ-2026-0318 logged', ip: '192.168.1.61' },
    { timestamp: '26 Jun 2026, 11:20', user: 'Tom Bradley', action: 'Login', module: 'Authentication', details: 'Successful login', ip: '192.168.1.10' }
  ],

  documents: [
    { name: 'Rooming List — GRB-2026-0847.pdf', category: 'Rooming Lists', booking: 'GRB-2026-0847', uploadedBy: 'Sarah Mitchell', date: '25 Jun 2026', size: '245 KB', icon: 'pdf' },
    { name: 'Contract — TUI UK 2026.pdf', category: 'Contracts', booking: '—', uploadedBy: 'Richard Whitmore', date: '15 Jan 2026', size: '1.2 MB', icon: 'pdf' },
    { name: 'Proforma Invoice — GRB-2026-0860.xlsx', category: 'Invoices', booking: 'GRB-2026-0860', uploadedBy: 'Laura Green', date: '22 Jun 2026', size: '89 KB', icon: 'excel' },
    { name: 'Coach Parking Plan — Brighton.pdf', category: 'Operational', booking: 'GRB-2026-0847', uploadedBy: 'Sarah Mitchell', date: '24 Jun 2026', size: '512 KB', icon: 'pdf' },
    { name: 'Menu Selection — Highland Heritage.docx', category: 'F&B', booking: 'GRB-2026-0851', uploadedBy: 'Andrew Fraser', date: '20 Jun 2026', size: '156 KB', icon: 'word' },
    { name: 'Allocation Sheet — Lake District.xlsx', category: 'Rooming Lists', booking: 'GRB-2026-0864', uploadedBy: 'Emma Richardson', date: '26 Jun 2026', size: '178 KB', icon: 'excel' },
    { name: 'Agency Rate Agreement — Riviera 2026.pdf', category: 'Contracts', booking: '—', uploadedBy: 'Richard Whitmore', date: '01 Feb 2026', size: '890 KB', icon: 'pdf' },
    { name: 'Special Requests — Cotswolds Discovery.pdf', category: 'Correspondence', booking: 'GRB-2026-0855', uploadedBy: 'James Holloway', date: '21 Jun 2026', size: '67 KB', icon: 'pdf' }
  ],

  roles: [
    { name: 'Administrator', users: 1, description: 'Full system access including user management and settings' },
    { name: 'Reservations Manager', users: 1, description: 'Manage bookings, enquiries, and agency relationships' },
    { name: 'Reservations Coordinator', users: 2, description: 'Create and update bookings, upload documents' },
    { name: 'Hotel Manager', users: 4, description: 'View and manage hotel-specific bookings and allocations' },
    { name: 'Finance Manager', users: 1, description: 'Access revenue, payments, and financial reports' },
    { name: 'Read Only', users: 0, description: 'View-only access to dashboards and reports' }
  ]
};

/* Render helpers for dashboard */
function formatCurrency(amount) {
  return '£' + amount.toLocaleString('en-GB', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

function renderDashboardCharts() {
  const barContainer = document.getElementById('revenueChartBars');
  if (barContainer && HGBMS_DATA.monthlyRevenue) {
    const max = Math.max(...HGBMS_DATA.monthlyRevenue.map(function (m) { return m.value; }));
    barContainer.innerHTML = HGBMS_DATA.monthlyRevenue.map(function (m) {
      const h = Math.round((m.value / max) * 100);
      return '<div class="chart-bar" style="height:' + h + '%" data-bs-toggle="tooltip" title="' + formatCurrency(m.value) + '"></div>';
    }).join('');

    const labels = document.getElementById('revenueChartLabels');
    if (labels) {
      labels.innerHTML = HGBMS_DATA.monthlyRevenue.map(function (m) {
        return '<span>' + m.month + '</span>';
      }).join('');
    }
  }

  const legend = document.getElementById('agencyPieLegend');
  if (legend && HGBMS_DATA.agencyShare) {
    legend.innerHTML = HGBMS_DATA.agencyShare.map(function (a) {
      return '<div class="pie-legend-item">' +
        '<span class="pie-legend-dot" style="background:' + a.color + '"></span>' +
        '<span class="flex-grow-1">' + a.name + '</span>' +
        '<span class="fw-semibold">' + a.pct + '%</span></div>';
    }).join('');
  }
}

function renderDashboardTables() {
  const arrivals = document.getElementById('upcomingArrivalsBody');
  if (arrivals) {
    arrivals.innerHTML = HGBMS_DATA.upcomingArrivals.map(function (r) {
      return '<tr>' +
        '<td><a href="group-booking-detail.html" class="fw-semibold">' + r.ref + '</a></td>' +
        '<td>' + r.group + '</td>' +
        '<td>' + r.hotel + '</td>' +
        '<td>' + r.agency + '</td>' +
        '<td>' + r.arrival + '</td>' +
        '<td class="text-center">' + r.rooms + '</td>' +
        '<td class="text-center">' + r.pax + '</td></tr>';
    }).join('');
  }

  const departures = document.getElementById('upcomingDeparturesBody');
  if (departures) {
    departures.innerHTML = HGBMS_DATA.upcomingDepartures.map(function (r) {
      return '<tr>' +
        '<td><a href="group-booking-detail.html" class="fw-semibold">' + r.ref + '</a></td>' +
        '<td>' + r.group + '</td>' +
        '<td>' + r.hotel + '</td>' +
        '<td>' + r.agency + '</td>' +
        '<td>' + r.departure + '</td>' +
        '<td class="text-center">' + r.rooms + '</td>' +
        '<td class="text-center">' + r.pax + '</td></tr>';
    }).join('');
  }

  const enquiries = document.getElementById('latestEnquiriesBody');
  if (enquiries) {
    const statusClass = { 'New': 'enquiry', 'Follow Up': 'pending', 'Quoted': 'partial', 'Confirmed': 'confirmed', 'Lost': 'cancelled', 'Cancelled': 'cancelled' };
    enquiries.innerHTML = HGBMS_DATA.latestEnquiries.map(function (e) {
      return '<tr>' +
        '<td><a href="enquiries.html" class="fw-semibold">' + e.ref + '</a></td>' +
        '<td>' + e.company + '</td>' +
        '<td>' + e.contact + '</td>' +
        '<td>' + e.subject + '</td>' +
        '<td><span class="badge-status badge-' + (statusClass[e.status] || 'pending') + '">' + e.status + '</span></td>' +
        '<td>' + e.date + '</td></tr>';
    }).join('');
  }

  const topHotels = document.getElementById('topHotelsBody');
  if (topHotels) {
    topHotels.innerHTML = HGBMS_DATA.topHotels.map(function (h, i) {
      return '<tr>' +
        '<td class="text-muted">' + (i + 1) + '</td>' +
        '<td class="fw-semibold">' + h.name + '</td>' +
        '<td>' + h.city + '</td>' +
        '<td class="text-center">' + h.groups + '</td>' +
        '<td>' + formatCurrency(h.revenue) + '</td>' +
        '<td><div class="d-flex align-items-center gap-2"><div class="progress progress-thin flex-grow-1"><div class="progress-bar bg-success" style="width:' + h.occupancy + '%"></div></div><span class="small">' + h.occupancy + '%</span></div></td></tr>';
    }).join('');
  }

  const activity = document.getElementById('recentActivityTimeline');
  if (activity) {
    activity.innerHTML = HGBMS_DATA.recentActivity.map(function (a) {
      return '<div class="timeline-item">' +
        '<div class="timeline-dot ' + (a.type === 'danger' ? 'danger' : a.type === 'warning' ? 'warning' : a.type === 'info' ? 'info' : '') + '"></div>' +
        '<div class="timeline-time">' + a.time + '</div>' +
        '<div class="timeline-text">' + a.text + '</div></div>';
    }).join('');
  }

  const notifList = document.getElementById('notificationList');
  if (notifList) {
    notifList.innerHTML = HGBMS_DATA.notifications.map(function (n) {
      return '<div class="notification-item' + (n.unread ? ' unread' : '') + '">' +
        '<div class="notification-item-title">' + n.title + '</div>' +
        '<div class="notification-item-text">' + n.text + '</div>' +
        '<div class="notification-item-time">' + n.time + '</div></div>';
    }).join('');
  }
}

function populateDashboardStats() {
  const s = HGBMS_DATA.stats;
  const map = {
    statHotels: s.totalHotels,
    statCompanies: s.totalCompanies,
    statAgencies: s.totalAgencies,
    statActiveGroups: s.activeGroups,
    statTodayArrivals: s.todayArrivals,
    statTodayDepartures: s.todayDepartures,
    statPendingEnquiries: s.pendingEnquiries,
    statCancelledGroups: s.cancelledGroups,
    statRevenue: formatCurrency(s.revenue),
    statRoomNights: s.roomNights.toLocaleString('en-GB'),
    statAdr: '£' + s.averageAdr.toFixed(2),
    statBreakfast: formatCurrency(s.breakfastRevenue),
    statDinner: formatCurrency(s.dinnerRevenue),
    statOutstanding: formatCurrency(s.outstandingPayments)
  };

  Object.keys(map).forEach(function (id) {
    const el = document.getElementById(id);
    if (el) el.textContent = map[id];
  });
}

document.addEventListener('DOMContentLoaded', function () {
  if (document.body.dataset.page === 'dashboard') {
    populateDashboardStats();
    renderDashboardCharts();
    renderDashboardTables();
    if (typeof bootstrap !== 'undefined') {
      document.querySelectorAll('#revenueChartBars .chart-bar').forEach(function (el) {
        new bootstrap.Tooltip(el);
      });
    }
  }
});
