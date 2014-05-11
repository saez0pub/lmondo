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

$('#logout').click(function() {
    $.get("ajax/logout.php", function(data) {
        window.location = 'index.php';
    });
});
$(document).on('hidden.bs.modal', function(e) {
    $(e.target).removeData('bs.modal');
})
$('#myModal').on('shown.bs.modal', function() {
    $("#myModal button.save").click(function(ev) {
        $.ajax({
            type: "POST",
            url: ev.target.getAttribute('href'),
            data: $('#modalForm').serialize(),
            success: function(msg) {
                console.log(msg);
                $("#myModal").modal('hide');
                window.location.reload()
            },
            error: function(msg) {
                console.log(msg);
                alert("Error : " + msg.status + " " + msg.statusText + " " + msg.responseText);
                window.location.reload()
            }
        });
    });
});