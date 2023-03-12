{% block initialize %}

{% endblock %}

{% block insert_item %}

{% endblock %}

{% block insert_list %}

{% endblock %}
{% block delete_item %}

{% endblock %}

{% block delete_list %}

{% endblock %}

{% block delete_tree %}

{% endblock %}

{% block update_item %}

{% endblock %}

{% block replace_item %}

{% endblock %}

{% block select_item %}

{% endblock %}

{% block select_list %}
-- 列表查询
SELECT * FROM `{{dbname}}`.`guide` 
WHERE 1 = 1
  {% if parent is not null %} AND `parent` = {{parent}} {% endif %}

-- ORDER BY id;

{% endblock %}

{% block select_tree %}
-- 树状查询

{% endblock %}