Perfect — now the workflow becomes a **3-party flow**:

**Organisation → Admin → Trainer**
where **trainer is NOT assigned until org pays**, and admin then pays trainer after training ends.

This is very common in EdTech + marketplace platforms (UrbanCompany, Upwork escrow, Byju’s corporate training).

Below is the **clear, perfect final flow + DB changes**.

---

# ✅ **FINAL REQUIRED FLOW (Correct & Professional)**

---

# 🧩 **STEP 1 — Organisation Creates Training Requirement**

This creates a record in:

### ✔ training_requirements

```
requirement_id | org_id | program_id | mode | date | status = open
```

All eligible trainers see this.

---

# 🧩 **STEP 2 — Trainers See the Requirement & First Trainer Accepts**

• Trainers click **“Accept Requirement”**
• System checks using a DB transaction:

```
if requirement.status == "open":
    mark requirement as accepted_by = trainer_id
    requirement.status = 'pending_payment'   <-- NEW
else:
    show Already taken
```

Now:

✔ Requirement is closed for others
✔ Trainer is visible to org
✔ Booking is NOT yet created

---

# 🧩 **STEP 3 — Organisation sees: “Trainer X accepted. Pay to proceed”**

Organisation dashboard shows:

```
Trainer: Rahul Sharma
Charges: ₹2500
Status: Waiting for Payment
[Pay Now]
```

Once org clicks Pay:

✔ Payment goes to **Admin’s wallet**
✔ Booking becomes official
✔ Trainer becomes assigned

---

# 🧩 **STEP 4 — After Payment → Booking is Created**

Create `booking` record when payment is successful:

### ✔ booking table

```
booking_id
org_id
trainer_id   <- assigned now
training_status = 'assigned'
payment_status = 'paid'
transaction_id = <payment gateway id>
```

Also update:

```
training_requirements.status = 'assigned'
```

Now the trainer sees:

```
Your training has been confirmed.
Training will start on 15 Feb.
```

---

# 🧩 **STEP 5 — Training Happens (Offline/Online)**

Tracking flow stays the same:

* Out for travel
* Arrived
* Teaching started
* Progress (percentage)
* Completed
* Test
* Review

These statuses are stored in:

### ✔ booking_progress table

---

# 🧩 **STEP 6 — After completion → Trainer Requests Payment Release**

Trainer dashboard:

```
Training Completed
Request Payment from Admin?
[Request Payout]
```

Admin sees list:

```
Rahul Sharma → Booking #23
Amount Due: ₹2500
[Release Payment]
```

Admin approves → money is paid to trainer externally or via payout API.

---

# 📌 **IMPORTANT — PAYMENT LOGIC MUST BE STORED**

You need a table that stores ALL payments.

---

# 🚀 **NOW — DATABASE STRUCTURE YOU NEED**

Your existing tables stay the same.

You only need **two new mandatory tables** + 1 optional:

---

## ⭐ 1. training_requirements (mandatory)

```
requirement_id (PK)
org_id
program_id
mode
schedule_start
schedule_end
location fields
accepted_trainer_id  (nullable)
status (open, pending_payment, assigned, completed)
created_at
```

---

## ⭐ 2. booking_progress (mandatory)

```
progress_id
booking_id
status
percentage
note
created_at
```

---

## ⭐ 3. payments (mandatory for admin hold)

Payment details need a separate table.

### payments table:

```
payment_id
booking_id (nullable until booking created)
requirement_id
payer_type ('organisation' or 'admin')
payee_type ('admin' or 'trainer')
transaction_type ('training_fee', 'trainer_payout')
amount
transaction_id (from Razorpay/Stripe)
payment_status ('success','failed','pending')
created_at
```

Workflows:

* Organisation → Admin (training_fee)
* Admin → Trainer (trainer_payout)

---

# 🎯 **FINAL STATE DIAGRAM (Best-practice)**

```
Organisation posts requirement
            ↓
Requirement OPEN → visible to trainers
            ↓
First trainer accepts
Requirement status → pending_payment
Trainer marked as “accepted_by”
            ↓
Organisation pays admin
Payment status → success
            ↓
booking created
requirement → assigned
trainer_status → assigned
            ↓
Training starts
            ↓
Training completed
            ↓
Trainer requests payout
            ↓
Admin releases payment to trainer
            ↓
Process completed
```

Exactly how professional EdTech and gig-platforms work.

---

# 🧰 If you want, I can now provide:

### ✔ Full Laravel Migrations for:

* training_requirements
* payments
* booking_progress

### ✔ Models + relationships

### ✔ Controller logic

### ✔ Transaction-safe “First trainer wins” logic

### ✔ Payment flow implementation (Razorpay example)

Just reply: **"give me migrations + models"** or **"give me full code flow"**.

-----------------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------
                                                                Draft 2
-----------------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------

Got it.
You don’t want “send request to trainers”.
Instead, you want a **marketplace model** like:

* Organisation posts a **training requirement**
* All trainers who opted for that program can **see the requirement**
* First trainer who accepts → automatically assigned
* Requirement becomes **closed** for others

This is exactly how platforms like **UrbanCompany** or **Freelancer bidding with instant hire** work.

Below is the perfect flow + database changes.

---

# ✅ **Updated Flow (First Come – First Serve Trainer Assignment)**

### **1. Organisation creates a Training Requirement**

Organisation chooses:

* Program
* Location
* Mode (online/offline)
* Schedule
* Any notes

This creates a new **training requirement**.

---

# 🚀 **2. All eligible trainers see the requirement**

Eligible trainers are:

1. Training_mode matches (online/offline)
2. For_org_type matches
3. Trainer is verified
4. Trainer opted for this program

Their dashboard shows:

```
New Opportunity:
Program: Communication Skills
Location: Pune
Date: 12 Feb
Pay: ₹X
[Accept]
```

---

# 🥇 **3. First trainer to accept gets the booking**

When trainer clicks **"Accept"**:

### We check:

```
if booking.trainer_id is null:
    assign trainer
else:
    decline with message “Already taken by another trainer”
```

This ensures only **one trainer wins**.

---

# 🚫 **4. Requirement disappears for all other trainers**

Once booking.trainer_id is set:

* No one else sees this requirement
* Or they see status = “Taken by another trainer”

---

# 📍 **5. Now normal tracking flow starts**

* Trainer starts journey
* Arrives
* Starts teaching
* Progress updates
* Completion
* Test
* Review

---

# ✅ **Database Structure Needed Now (Revised)**

Your existing **booking** table can be reused.
No need for *program_invitations* anymore.

You only need **2 extra tables**:

---

## **1️⃣ training_requirements (NEW — this replaces invitations)**

This stores the *job posted by organisation*.

### **training_requirements table:**

| Column                  | Description                 |
| ----------------------- | --------------------------- |
| requirement_id          | PK                          |
| org_id                  | FK                          |
| program_id              | FK                          |
| mode                    | online/offline              |
| location (city, state…) | move from org?              |
| schedule_start          | datetime                    |
| schedule_end            | datetime                    |
| status                  | open / assigned / completed |
| payment                 | pending / completed / declined |
| created_at              |                             |

✔ Trainers will read from this table
✔ Once assigned → status = “assigned”

---

## **2️⃣ booking_progress (MANDATORY for tracking timeline)**

Your plain booking table is NOT enough to store the training stages.

### booking_progress table:

| Column      | Description                                                                                       |
| ----------- | ------------------------------------------------------------------------------------------------- |
| progress_id | PK                                                                                                |
| booking_id  | FK                                                                                                |
| status      | assigned / enroute / arrived / teaching_started / ongoing / completed / test_completed / reviewed |
| percentage  | nullable                                                                                          |
| timestamp   |                                                                                                   |

---

# 🧭 **Booking Table Logic (your existing one)**

Your existing booking table:

```
booking_id
org_id
trainer_id
trainer_status
training_status
org_review
org_rating
payment_status
transaction_id
```

When trainer clicks "Accept", you create:

### New booking:

```
booking_id: auto
org_id: requirement.org_id
trainer_id: trainer_id
trainer_status: accepted
training_status: assigned
```

Then:

```
training_requirements.status = "assigned"
```

---

# 🔁 **Final Process Flow (Step-by-Step)**

### ✔ Step 1: Organisation Posts Requirement

→ Insert into training_requirements
→ status = open

---

### ✔ Step 2: Trainers See All OPEN Requirements

Query:

```
training_requirements where status = 'open'
AND program_id in trainer.opted_programs
AND training_mode matches
```

---

### ✔ Step 3: Trainer Clicks “Accept”

Process:

```
BEGIN TRANSACTION

// check if requirement still open and no booking exists
if requirement.status == "open":
    // assign trainer
    create booking table entry
    update requirement.status = "assigned"
    COMMIT
else:
    ROLLBACK  
    return "Already taken"
```

**Using DB transaction ensures atomic (safe) assignment.**

---

### ✔ Step 4: Other Trainers Cannot See It

Because:

```
select * from training_requirements 
where status = 'open'
```

→ assigned requirements won't appear

---

### ✔ Step 5: Tracking Workflow Begins

Using booking_progress table.

---

# ⭐ You Now Need Only 2 New Tables:

1. **training_requirements**
2. **booking_progress**

If you want tracking map → optional 3rd table:

* trainer_tracking_logs

---

# If you want, I can now generate:

### ✔ migrations for these 2–3 tables

### ✔ Laravel models + relationships

### ✔ controller flow (organisation side + trainer side)

### ✔ transaction-safe “first come first serve” accept code

### ✔ routes

### ✔ dashboard queries

Just tell me:
**“give me migrations + models”** or **“give me full Laravel code flow”**.

-----------------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------
                                                                Draft 1
-----------------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------

Great — you already have **Trainer**, **Organisation**, **Program**, **Booking**, **Test** tables.
But for a **full training lifecycle + tracking flow**, these 5 tables are NOT enough.

To support the flow you want (trainer invitation → accept → travel → arrival → teaching started → progress → test → review), you **must** add around **4–6 additional tables**.

Below I’m giving exactly what you need **and why** — this is the cleanest and most scalable structure for a training platform.

---

# ✅ **1. program_invitations (mandatory)**

Why?
Because *booking* only stores a single trainer assigned AFTER acceptance.
But before that, you need to send invitations to multiple trainers.

### **Table:** program_invitations

| Column        | Description                   |
| ------------- | ----------------------------- |
| invitation_id | PK                            |
| program_id    | FK                            |
| trainer_id    | FK                            |
| status        | pending / accepted / rejected |
| invited_at    | datetime                      |
| responded_at  | datetime                      |

✔ This solves trainer-selection workflow
✔ Allows multiple invitations
✔ Booking will only be created when trainer **accepts**

---

# ✅ **2. booking_details / booking_progress (mandatory)**

The `booking` table is too small to store step-by-step status.

You need a table to store the **training journey** timeline:

### **Table:** booking_progress

| Column      | Description                                 |
| ----------- | ------------------------------------------- |
| progress_id | PK                                          |
| booking_id  | FK                                          |
| status      | (enum)                                      |
| note        | optional (comments like “Started Module 2”) |
| percentage  | nullable                                    |
| created_at  | timestamp                                   |

### Status Flow to store:

```
Assigned
Out_for_Travel
Arrived
Teaching_Started
Ongoing
Completed
Test_Completed
Reviewed
```

✔ You can show a timeline to organisation
✔ Helps in auditing and analytics

---

# ✅ **3. trainer_tracking_logs (for offline mode tracking)**

If you want to show **Google map tracking** like Uber or Swiggy.

### **Table: trainer_tracking_logs**

| Column     | Description                       |
| ---------- | --------------------------------- |
| track_id   | PK                                |
| booking_id | FK                                |
| latitude   | double                            |
| longitude  | double                            |
| accuracy   | optional                          |
| event      | start_journey / enroute / arrived |
| timestamp  | created_at                        |

✔ Only required for offline mode
✔ Used for live map
✔ Stores trainer movement for audit

---

# ✅ **4. program_modules (optional but recommended)**

If you want automated training progress.

### Table: program_modules

| Column     | Description     |
| ---------- | --------------- |
| module_id  | PK              |
| program_id | FK              |
| title      | module name     |
| percentage | module weight   |
| order_no   | module sequence |

✔ Useful for % based tracking
✔ trainers mark modules complete → system auto updates %

---

# ✅ **5. program_sessions (optional but professional)**

If your programs have daily sessions.

### Table: program_sessions

| Column         | Description                   |
| -------------- | ----------------------------- |
| session_id     | PK                            |
| booking_id     | FK                            |
| module_id      | FK (optional)                 |
| session_date   | date                          |
| start_time     | datetime                      |
| end_time       | datetime                      |
| session_status | pending / started / completed |

✔ Useful for attendance
✔ Auto progress
✔ Perfect for organisations to view daily updates

---

# ✅ **6. test_results (MANDATORY for test feature)**

Your current `test` table only stores metadata, not results.

### Table: test_results

| Column         | Description                    |
| -------------- | ------------------------------ |
| result_id      | PK                             |
| test_id        | FK                             |
| booking_id     | FK                             |
| trainee_id     | FK (if you have student table) |
| marks_obtained | int                            |
| status         | pass / fail                    |
| feedback       | optional                       |

✔ Allows trainees to take tests
✔ Can generate performance reports
✔ Can show trainer effectiveness

---

# 🧩 **Your Current Tables + What You Need**

### You have:

* trainer
* organisation
* program
* booking
* test

### You must add:

1. **program_invitations**
2. **booking_progress**
3. **trainer_tracking_logs**
4. **test_results**
5. *(optional but recommended)* program_modules
6. *(optional but useful)* program_sessions

---

# 🧭 **Final Architecture (enterprise-grade)**

```
Organisation 
   │
   ├── Creates Program
   │
   ├── program_invitations
   │       └── Sent to Trainers
   │
Trainer Accepts
   │
Booking created  →  booking (main)
   │
   ├── booking_progress (timeline)
   │
   ├── trainer_tracking_logs (offline only)
   │
   ├── program_sessions (if needed)
   │
   ├── test
   │        └── test_results
   │
   └── review stored in booking table
```

---

# ⭐ If you want, I can also give:

### ✔ migrations for all 6 missing tables

### ✔ full Laravel relationships for each model

### ✔ API design

### ✔ trainer flow + org dashboard UI flow

### ✔ timeline UI component (HTML/Sneat-ready)

Just tell me:
**“give me migrations + models”** or **“give me complete API structure”**