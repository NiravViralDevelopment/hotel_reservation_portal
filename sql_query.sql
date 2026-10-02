Hiten sql query :



ERROR FIX: travel_agencies.country cannot be null



Table: travel_agencies

Column: country



The app now defaults blank country to "United Kingdom" (same as existing rows).

You do NOT need to run SQL for the popup to work.



OPTIONAL (only if you want country to allow NULL in DB):


ALTER TABLE travel_agencies

  MODIFY country VARCHAR(191) NULL DEFAULT 'United Kingdom';


ALTER TABLE enquiries

  ADD COLUMN check_in DATE NULL AFTER day,

  ADD COLUMN check_out DATE NULL AFTER check_in;


ALTER TABLE enquiries

  ADD COLUMN has_tax TINYINT(1) NOT NULL DEFAULT 0 AFTER total_revenue,

  ADD COLUMN tax_percentage DECIMAL(5,2) NULL AFTER has_tax,

  ADD COLUMN tax_revenue DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER tax_percentage;
