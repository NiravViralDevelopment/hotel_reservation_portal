# Hotel Group Booking Management System (HGBMS)

**Client Documentation**  
**Version:** 1.0  
**Date:** 29 September 2026  
**Product:** Hotel Group Booking Management System — United Kingdom  

---

## 1. Purpose

HGBMS is a web-based portal for managing **hotel group bookings** end to end: enquiries, confirmed contracts, cancellations, arrivals/departures, revenue, and user access control.

It is designed for hotel groups and reservation teams who need one place to:

- Track group enquiries and convert them into bookings  
- Manage active group contracts (rooms, rates, payment, CXL policy)  
- Monitor cancelled business and revenue impact  
- View day-to-day arrivals and departures  
- Control who can see and edit which data (roles & permissions)  

---

## 2. Who Uses the System

| Role | Typical use |
|------|-------------|
| **Administrator** | Full access to all modules, users, roles, and hotels |
| **Operations / Reservations** | Enquiries, group bookings, arrivals, departures, calendar |
| **Finance / Commercial** | Revenue views, payment status, commission, reports |
| **Limited hotel users** | Access only to hotels assigned to their account |

Access is controlled by **roles and permissions**. Inactive users cannot sign in.

---

## 3. Signing In

- Secure login with email and password  
- Forgot-password flow to reset access  
- Profile available from the header user menu (name, role, sign out)  

After login, users land on the **Dashboard**.

---

## 4. System Modules Overview

### 4.1 Main

| Module | What it does |
|--------|----------------|
| **Dashboard** | Snapshot of booking activity, key figures, and upcoming groups |
| **Hotels** | Property list (name, code, location, rooms, manager, status). Search and filter supported |
| **Companies** | Parent / hotel-group companies linked to properties |
| **Travel Agencies** | Agency partners that send group business |
| **Contacts** | Contact directory for companies and agencies |

### 4.2 Bookings

| Module | What it does |
|--------|----------------|
| **Enquiries** | Incoming group enquiries (pipeline). Confirmed and cancelled enquiries leave this list and move to the correct module |
| **Group Bookings** | Active / definite group contracts |
| **Cancelled Bookings** | Cancelled groups with reason and revenue lost |
| **Arrivals** | Groups arriving on a selected date |
| **Departures** | Groups departing on a selected date |
| **Calendar** | Visual overview of stay dates |

### 4.3 Finance

| Module | What it does |
|--------|----------------|
| **Revenue** | Revenue summaries (e.g. by period) |
| **Reports** | Runnable operational / commercial reports |

### 4.4 Administration

| Module | What it does |
|--------|----------------|
| **Users** | Create and manage accounts, assign roles and hotels, activate/deactivate |
| **Roles** | Define roles and permissions (what each role can see/do) |
| **Audit Logs** | Activity history (who did what, when) |

> **Note:** A dedicated Documents menu may be hidden in some deployments. Documents can still be attached on a **group booking** edit screen.

---

## 5. Core Business Flow

```
Enquiry (pipeline)
    │
    ├─ Confirm booking  ──►  Group Bookings module
    │
    └─ Cancel (+ reason) ──►  Cancelled Bookings module
```

### 5.1 Enquiries

- Create and edit enquiries (group name, hotel, agency, nights, room mix & rates)  
- **Total revenue** is calculated automatically:  
  `(single rooms × rate + double rooms × rate + triple rooms × rate) × nights`  
- On **Edit**, two actions are available:  
  - **Confirm booking** → creates/updates a group booking and opens Group Bookings  
  - **Cancel** → **cancellation reason is required**, then the record appears under Cancelled Bookings  
- Enquiry list shows only open pipeline items (not confirmed/cancelled)  
- List actions: **View**, **Edit**, **Delete** (by permission)

### 5.2 Group Bookings

For each booking you can manage:

- Block ID, group name, hotel, company, agency, contact  
- Arrival / departure / nights / rooms / pax / status / revenue  
- **Payment Terms and Conditions**  
  - Payment Term  
  - Due Date  
  - Payment Status  
- **CXL Policy**  
  - CXL Policy  
  - CXL Due Date  
  - CXL Date  
- **Commission**  
- **Attach document** (upload / download / remove files on the booking)

### 5.3 Cancelled Bookings

- Lists cancelled contracts  
- Shows cancellation date, revenue lost, and reason  

---

## 6. Key Features for Daily Operations

| Feature | Benefit |
|---------|---------|
| Search & filters | Find hotels, companies, users, bookings quickly |
| Column sorting | Sort list tables by clicking column headers |
| Hotel assignment | Restrict non-admin users to their assigned hotels |
| Status badges | Clear visual status (enquiry / booking / payment) |
| Soft user control | Deactivate users instead of hard-delete when linked to roles/hotels |
| Audit trail | Track important changes for compliance and support |

---

## 7. Data Model (Simplified)

| Area | Main records |
|------|----------------|
| Organisation | Companies → Hotels → Users (via hotel assignment) |
| Partners | Travel Agencies, Contacts |
| Pipeline | Enquiries → (optional) Group Bookings |
| Operations | Arrivals, Departures, Calendar |
| Commercial | Revenue, Payment terms, Commission, CXL fields |
| Files | Documents linked to a group booking |
| Security | Users, Roles, Permissions, Audit logs |

---

## 8. Security & Access Control

- Authentication required for all business pages  
- Role-based permissions (view / create / edit / delete / convert as applicable)  
- Administrator bypass for full system control  
- Users can be set **inactive** to block login without removing history  
- Hotel-scoped data for non-administrator users  

---

## 9. Technology Overview (for IT)

| Layer | Technology |
|-------|------------|
| Frontend | Responsive web UI (Bootstrap-based), light theme |
| Backend | Laravel (PHP) |
| Database | Relational DB (MySQL / MariaDB via WAMP or production equivalent) |
| Access | Spatie roles & permissions |

The `html/` prototype screens define the original UX reference; the live application delivers the same product areas as a secured multi-user system.

---

## 10. Prototype Screens (HTML Reference)

The design/prototype pack includes screens such as:

| Screen | File |
|--------|------|
| Login | `html/index.html` |
| Dashboard | `html/dashboard.html` |
| Hotels / Hotel detail | `html/hotels.html`, `html/hotel-detail.html` |
| Companies / Company detail | `html/companies.html`, `html/company-detail.html` |
| Travel agencies | `html/travel-agencies.html` |
| Contacts | `html/contacts.html` |
| Enquiries | `html/enquiries.html` |
| Group bookings / Detail | `html/group-bookings.html`, `html/group-booking-detail.html` |
| Cancelled bookings | `html/cancelled-bookings.html` |
| Arrivals / Departures | `html/arrivals.html`, `html/departures.html` |
| Calendar | `html/calendar.html` |
| Revenue / Reports | `html/revenue.html`, `html/reports.html` |
| Documents | `html/documents.html` |
| Users / Roles / Audit / Settings / Profile | `html/users.html`, `html/roles.html`, `html/audit-logs.html`, `html/settings.html`, `html/profile.html` |

These HTML pages are useful for demos and UX review. Day-to-day use is through the live application URL provided by your team.

---

## 11. Recommended Client Demo Path

1. Sign in as Administrator  
2. Open **Dashboard**  
3. Create or open an **Enquiry** → set rooms/rates → see total revenue  
4. Tick **Confirm booking** → land in **Group Bookings**  
5. Edit booking → set Payment / CXL / Commission → attach a document  
6. Show **Arrivals** / **Departures** / **Calendar**  
7. From another enquiry, tick **Cancel** with a reason → show **Cancelled Bookings**  
8. Briefly show **Users / Roles** and **Audit Logs**  

---

## 12. Scope Notes for Stakeholders

**Included in current delivery (typical):**

- Full enquiry → booking / cancel workflow  
- Group booking commercial fields (payment, CXL, commission)  
- Document attach on booking edit  
- Hotels, companies, agencies, contacts  
- Revenue & reports entry points  
- Users, roles, permissions, audit logs  
- Sortable lists and hotel-based access for non-admins  

**May be limited / optional depending on deployment:**

- Standalone Documents menu  
- Settings module  
- Dark theme (system uses light theme)  

---

## 13. Support & Next Steps

For training, UAT, or go-live:

1. Confirm production URL and admin users  
2. Import or create hotels, companies, and agencies  
3. Assign users to hotels and roles  
4. Run a short UAT using the demo path above  
5. Agree backup and hosting ownership with IT  

---

**Document prepared for client review.**  
If you need this as PDF, Word, or a shorter one-page summary, say which format you prefer.
