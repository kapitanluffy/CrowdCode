Well, the templating engine still sucks xD

<div>
	<h4>Index</h4>
{@LOOP array_of_data}
{@array_of_data.[index]}<br/>
{!LOOP}
</div>

<div>
	<h4>Fruits</h4>
{@LOOP fruits}
{@fruits}<br/>
{!LOOP}
</div>