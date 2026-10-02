Hiten sql query :



ERROR FIX: travel_agencies.country cannot be null



Table: travel_agencies

Column: country



The app now defaults blank country to "United Kingdom" (same as existing rows).

You do NOT need to run SQL for the popup to work.



OPTIONAL (only if you want country to allow NULL in DB):



1)

ALTER TABLE travel_agencies

  MODIFY country VARCHAR(191) NULL DEFAULT 'United Kingdom';



2)

ADD check-in / check-out dates on enquiries (run this manually)



Table: enquiries



ALTER TABLE enquiries

  ADD COLUMN check_in DATE NULL AFTER day,

  ADD COLUMN check_out DATE NULL AFTER check_in;

