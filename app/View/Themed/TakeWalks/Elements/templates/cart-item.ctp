<script type="text/template" class="template template-cart-item">
    <div class="right-sidebar-item">
        <div class="sidebar-subheading">
            <a href="<%- tourUrl %>" class="event-title"><%- name %></a>
            <!-- Tues, 14 Mar, 2017 at 9:00 am -->
            <p class="event-date"><%-date %></p>

            <% Object.keys(guests).forEach(function(guest){ %>

                <div class="guest-select-row">
                    <div class="select-item">
                        <input type="number" value="<%= guests[guest]['amount'] %>" readonly>
                    </div>
                    <div class="guest-label">
                        <span><%- guest %></span>
                    </div>

                    <div class="guest-price"><%= guests[guest]['price'] %></div>
                </div>

            <% }); %>

            <a href="javascript:;" class="remove-from-cart"><i class="icon icon-remove_tour"></i> <span>Remove from cart</span></a>
        </div>
    </div>
</script>