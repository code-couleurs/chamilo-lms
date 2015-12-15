{% block header %}
{% include "layout/main_header.tpl" %}
{% endblock %}

{% block body %}
	{% if show_sniff == 1 %}
	 	{% include "layout/sniff.tpl" %}
	{% endif %}
{% endblock %}

{% block footer %}
    {#  Footer  #}
    {% if show_footer == true %}
        </div> <!-- end of #col" -->
        </div> <!-- end of #row" -->
        </div> <!-- end of #container" -->
    {% endif %}
    {% include "layout/main_footer.tpl" %}
{% endblock %}
