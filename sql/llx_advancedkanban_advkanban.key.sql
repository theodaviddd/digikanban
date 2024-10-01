-- Copyright (C) 2022 John Botella <john.botella@atm-consulting.fr>
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


ALTER TABLE llx_advancedkanban_advkanban ADD INDEX idx_advancedkanban_advkanban_rowid (rowid);
ALTER TABLE llx_advancedkanban_advkanban ADD INDEX idx_advancedkanban_advkanban_entity (entity);
ALTER TABLE llx_advancedkanban_advkanban ADD INDEX idx_advancedkanban_advkanban_ref (ref);
ALTER TABLE llx_advancedkanban_advkanban ADD INDEX idx_advancedkanban_advkanban_fk_soc (fk_soc);
ALTER TABLE llx_advancedkanban_advkanban ADD INDEX idx_advancedkanban_advkanban_fk_project (fk_project);
ALTER TABLE llx_advancedkanban_advkanban ADD CONSTRAINT llx_advancedkanban_advkanban_fk_user_creat FOREIGN KEY (fk_user_creat) REFERENCES llx_user(rowid);
ALTER TABLE llx_advancedkanban_advkanban ADD INDEX idx_advancedkanban_advkanban_status (status);

--ALTER TABLE llx_advancedkanban_advkanban ADD UNIQUE INDEX uk_advancedkanban_advkanban_fieldxy(fieldx, fieldy);

--ALTER TABLE llx_advancedkanban_advkanban ADD CONSTRAINT llx_advancedkanban_advkanban_fk_field FOREIGN KEY (fk_field) REFERENCES llx_advancedkanban_myotherobject(rowid);


