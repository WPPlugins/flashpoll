function fppp_deletePoll(polls_id)
{
    if (confirm('Are you sure you want to delete this poll?'))
    {
        document.forms['action'].fppp_action.value = 'delete_poll'; 
        document.forms['action'].fppp_arg1.value = polls_id; 
        document.forms['action'].submit();
    }
}


function fppp_editPoll(polls_id)
{
    document.forms['action'].fppp_action.value = 'edit_poll'; 
    document.forms['action'].fppp_arg1.value = polls_id; 
    document.forms['action'].submit();
}



function fppp_newChoice()
{
    var choices_div = document.getElementById('fppp_choices');
    
    
    var new_choice = document.createElement('input');
    new_choice.name = "fppp_new_choices[]";
    
    choices_div.appendChild(new_choice);
    choices_div.appendChild(document.createElement("br"));
}

function fppp_deleteChoice(poll_answers_id)
{
    document.forms['action'].fppp_arg2.value = 'delete_choice'; 
    document.forms['action'].fppp_arg3.value = poll_answers_id; 
    document.forms['action'].submit();
}