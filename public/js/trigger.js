/* 
 * Copyright (C) 2014 saez0pub
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
$('#myModal').on('shown.bs.modal', function() {
    $('#modalForm #divtype select').change(function() {
        $('#args').remove();
        if (this.selectedIndex != 0) {
            $.get("../ajax/dropdown.trigger.php?type=" + this.value + "&scenario_id=" + $('#scenario_id').attr('value'), function(data) {
                $('#divargs').append(data);
            });
        } else {
            $('#divargs').append('<input class="form-control" id="args" name="args" value="Veuillez sélectioner le Type" disabled>');
        }
    });
});